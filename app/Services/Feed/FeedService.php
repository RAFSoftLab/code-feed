<?php
namespace App\Services\Feed;

use App\Models\Commit;
use App\Models\Post;
use App\Services\AI\GoogleAIService;
use App\Services\AI\LLMService;
use App\Services\Git\GitRepositoryService;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FeedService
{
    // Define weights for each factor
    const WEIGHT_BOTH_FLAGS = 3;
    const WEIGHT_ONE_FLAG = 2;
    const WEIGHT_LENGTH = 1;
    const MAX_TIME_DECAY_FACTOR = 0.99; // Maximum time decay factor
    const MAX_HOUR_DIFFERENCE = 24 * 365; // Maximum time difference in hours (1 year)

    public function getFeed(): Collection
    {
        $posts = Post::with('commit')->get();

        return $this->rankPosts($posts);
    }

    private function rankPosts(Collection $posts): Collection
    {
        // Define ranking function for each commit
        $rankFunction = function ($post) {
            $score = 0;

            // Check for both flags true
            if ($post->commit->hasBugs && $post->commit->hasSecurityIssues) {
                $score += self::WEIGHT_BOTH_FLAGS;
            } else if ($post->commit->hasBugs || $post->commit->hasSecurityIssues) {
                $score += self::WEIGHT_ONE_FLAG;
            }

            // Add score based on length
            $score += self::WEIGHT_LENGTH * $post->commit->lineCount;

            // Apply time decay based on Carbon dates
            $now = Carbon::now();
            $postCreatedAt = Carbon::parse($post->commit->created_at);
            $hourDelta = $now->diffInHours($postCreatedAt);

            // Limit hour difference and calculate decay factor
            $limitedHourDelta = min($hourDelta, self::MAX_HOUR_DIFFERENCE);
            $decayFactor = pow(self::MAX_TIME_DECAY_FACTOR, $limitedHourDelta);

            $score *= $decayFactor;
            // Ensure a minimum score of 0.1

            return max($score, 0.1);
        };

        // Sort commits by decreasing rank score
        return $posts->sortByDesc($rankFunction);
    }

    public function loadFreshFeed(string $gitRepositoryURL, LLMService $aiService): void
    {
        $gitRepositoryService = new GitRepositoryService($gitRepositoryURL);
        $gitRepositoryService->cloneRepository();
        $commits = $gitRepositoryService->getCommits();
        $this->deleteAllCommitsAndPosts($gitRepositoryService);

        foreach ($commits as $commit) {
            $commitChanges = $gitRepositoryService->getCommitChanges($commit->getHash());
            $issues = $aiService->findIssues($commitChanges);


            $commitModel = $this->createCommitModel($commit, $commitChanges, $gitRepositoryService, $issues);
            $this->createPosts($commitModel, $aiService);
        }
        $gitRepositoryService->cleanUp();
    }

    private function createPosts(Commit $commit, LLMService $aiService): void
    {
        if (empty($commit->summary))
            $summaries = $aiService->summarize($commit);
        else $summaries = array();

        foreach ($summaries as $summary) {
            $post = new Post();
            $post->title = '';
            $post->content = $summary;
            $post->commit_id = $commit->id;
            $post->save();
        }
    }

    /**
     * @param mixed $commit
     * @param string $commitChanges
     * @param GitRepositoryService $gitRepositoryService
     * @param array $issues
     * @return mixed
     */
    private function createCommitModel(mixed $commit, string $commitChanges, GitRepositoryService $gitRepositoryService, array $issues): Commit
    {
        $commitModel = Commit::create([
            'author_name' => $commit->getAuthorName(),
            'author_email' => $commit->getAuthorEmail(),
            'title' => $commit->getSubjectMessage(),
            'summary' => $commit->getBodyMessage(),
            'lineCount' => substr_count($commitChanges, "\n") + 1,
            'repository' => $gitRepositoryService->getRepositoryName(),
            'organization' => $gitRepositoryService->getOrganizationName(),
            'hasSecurityIssues' => $issues['hasSecurityIssues'],
            'hasBugs' => $issues['hasBugs'],
            'hash' => $commit->getHash(),
            'created_at' => Carbon::create($commit->getAuthorDate()),
            'committed_at' => $commit->getAuthorDate(),
            'change' => $commitChanges,
        ]);
        return $commitModel;
    }

    /**
     * @param GitRepositoryService $gitRepositoryService
     * @return void
     */
    public function deleteAllCommitsAndPosts(GitRepositoryService $gitRepositoryService): void
    {
        Commit::where('organization', $gitRepositoryService->getOrganizationName())
            ->where('repository', $gitRepositoryService->getRepositoryName())
            ->delete();
    }
}
