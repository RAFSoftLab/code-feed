<?php

namespace App\Console\Commands;

use App\Models\Commit;
use App\Models\Post;
use App\Services\AI\GoogleAIService;
use Carbon\Carbon;
use Gitonomy\Git\Admin;
use Gitonomy\Git\Tree;
use Illuminate\Console\Command;

class LoadGitRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-git-repository {githubRepository=https://github.com/RAFSoftLab/code-feed-test-repo.git}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads a git repository from web';

    /**
     * Execute the console command.
     */
    public function handle(GoogleAIService $aiService): void
    {
        $githubRepository = $this->argument('githubRepository');
        $gitRootDir = $this->generateTmpDir();

        $repository = Admin::cloneTo($gitRootDir, $githubRepository, false);
        $log = $repository->getLog();
        $commits = $log->getCommits();

        // pattern for ssh links
        // $pattern = '/:(.*)\/(.*).git/';
        // preg_match($pattern, $githubRepository, $matches);
        preg_match('~github\.com/([^/]+)/([^/.]+?)(?:\.git)?$~', $githubRepository, $matches);
        $organizationName = $matches[1];
        $repositoryName = $matches[2];

        Commit::where('organization', $organizationName)
            ->where('repository', $repositoryName)
            ->delete();

        foreach ($commits as $commit) {
            $parsedTitleAndSummary = $this->parseTitleAndSummary($commit->getMessage());
            $commitChanges = $this->getCommitChanges($commit->getHash(), $gitRootDir);
            $issues = $aiService->findIssues($commitChanges);

            $commitModel = Commit::create([
                'author_name' => $commit->getAuthorName(),
                'author_email' => $commit->getAuthorEmail(),
                'title' => $parsedTitleAndSummary['title'],
                'summary' => $parsedTitleAndSummary['summary'],
                'repository' => $repositoryName,
                'organization' => $organizationName,
                'hasSecurityIssues' => $issues['hasSecurityIssues'],
                'hasBugs' => $issues['hasBugs'],
                'hash' => $commit->getHash(),
                'created_at' => Carbon::create($commit->getAuthorDate()),
                'committed_at' => $commit->getAuthorDate(),
                'change' => $commitChanges,
            ]);
            $this->createPosts($commitModel, $aiService);
        }

        $this->removeDirectory($gitRootDir);
    }

    private function createPosts(Commit $commit, GoogleAIService $aiService): void
    {
        $summaries = $aiService->summarize($commit);
        foreach ($summaries as $summary) {
            $post = new Post();
            $post->title = 'Commit!';
            $post->content = $summary;
            $post->commit_id = $commit->id;
            $post->save();
        }
    }

    private function getCommitChanges(string $commitHash, $gitDirectory): string
    {
        exec("cd $gitDirectory && git --no-pager show $commitHash", $output);
        return implode("\n", $output);
    }

    private function parseTitleAndSummary(string $commitMessage): array
    {
        $pattern = '/^(?P<title>.+?)\n{2}(?P<summary>.+)/s';
        if (preg_match($pattern, $commitMessage, $matches)) {
            return [
                'title' => $matches['title'],
                'summary' => $matches['summary']
            ];
        }
        return [
            'title' => $commitMessage,
            'summary' => ''
        ];
    }

    private function generateTmpDir(): string
    {
        $dirname = sys_get_temp_dir().DIRECTORY_SEPARATOR.time().'repo';
        mkdir($dirname);

        return $dirname;
    }

    private function removeDirectory($dir): void
    {
        system('rm -rf '.$dir);
    }
}
