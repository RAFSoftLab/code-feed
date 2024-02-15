<?php
namespace App\Services\Feed;

use App\Models\Commit;
use App\Models\Post;
use App\Models\Repository;
use App\Models\User;
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
    private LLMService $aiService;
    private string $gitRepositoryURL;
    private ?User $user;

    public function __construct(LLMService $aiService, string $gitRepositoryUrl = '', User $user = null)
    {
        $this->aiService = $aiService;
        $this->gitRepositoryURL = $gitRepositoryUrl;
        $this->user = $user;

    }

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

    public function loadFreshFeed(): void
    {
        $gitRepositoryService = new GitRepositoryService($this->gitRepositoryURL, $this->user);
        $this->deleteRepository($gitRepositoryService);

        $commits = $gitRepositoryService->getCommits();
        $repository = Repository::create(
            [
                'name' => $gitRepositoryService->getRepositoryName(),
                'organization' => $gitRepositoryService->getOrganizationName(),
                'user_id' => $this->user->id,
                'url' => $this->gitRepositoryURL,
                'description' => '',
            ]
        );
        foreach ($commits as $commit) {
            $commitChanges = $gitRepositoryService->getCommitChanges($commit->getHash());
            $issues = $this->aiService->findIssues($commitChanges);
            $commitModel = $this->createCommitModel($commit, $commitChanges, $gitRepositoryService, $issues, $repository);
            $this->createPosts($commitModel, $this->aiService);
        }
    }

    public function updateFeed(): void
    {
        $gitRepositoryService = new GitRepositoryService($this->gitRepositoryURL, $this->user);
        $commits = $gitRepositoryService->getNewCommits();

        foreach ($commits as $commit) {
            $commitChanges = $gitRepositoryService->getCommitChanges($commit->getHash());
            $issues = $this->aiService->findIssues($commitChanges);

            $commitModel = $this->createCommitModel($commit, $commitChanges, $gitRepositoryService, $issues);
            $this->createPosts($commitModel, $this->aiService);
        }
    }

    private function createPosts(Commit $commit, LLMService $aiService): void
    {
        if (empty($commit->summary))
            $summaries = $aiService->summarize($commit);
        else $summaries = [$commit->summary];

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
    private function createCommitModel(
        mixed $commit,
        string $commitChanges,
        GitRepositoryService $gitRepositoryService,
        array $issues,
        Repository $repository
    ): Commit
    {
        return Commit::create([
            'author_name' => $commit->getAuthorName(),
            'author_email' => $commit->getAuthorEmail(),
            'title' => $commit->getSubjectMessage(),
            'summary' => $commit->getBodyMessage(),
            'lineCount' => substr_count($commitChanges, "\n") + 1,
            'hasSecurityIssues' => $issues['hasSecurityIssues'],
            'hasBugs' => $issues['hasBugs'],
            'hash' => $commit->getHash(),
            'created_at' => Carbon::create($commit->getAuthorDate()),
            'committed_at' => $commit->getAuthorDate(),
            'change' => $commitChanges,
            'repository_id' => $repository->id
        ]);
    }

    private function deleteRepository(GitRepositoryService $gitRepositoryService): void
    {
        $gitRepositoryService->cleanUp();
        Repository::where('user_id', $this->user->id)
            ->where('organization', $gitRepositoryService->getOrganizationName())
            ->where('name', $gitRepositoryService->getRepositoryName())
            ->delete();
    }
}
