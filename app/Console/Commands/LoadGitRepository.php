<?php

namespace App\Console\Commands;

use App\Models\Commit;
use Gitonomy\Git\Admin;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class LoadGitRepository extends Command  implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-git-repository {repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads a git repository from web';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $githubRepository = $this->argument('repository');

        $gitRootDir = $this->generateTmpDir();

        $repository = Admin::cloneTo($gitRootDir, $githubRepository, false);
        $log = $repository->getLog();
        $commits = $log->getCommits();

        $this->removeDirectory($gitRootDir);

        Commit::truncate();
        // pattern for ssh links
        // $pattern = '/:(.*)\/(.*).git/';
        // preg_match($pattern, $githubRepository, $matches);
        preg_match('~github\.com/([^/]+)/([^/.]+?)(?:\.git)?$~', $githubRepository, $matches);
        $organizationName = $matches[1];
        $repositoryName = $matches[2];
        foreach ($commits as $commit) {
            $parsedTitleAndSummary = $this->parseTitleAndSummary($commit->getMessage());
            Commit::create([
                'author_name' => $commit->getAuthorName(),
                'author_email' => $commit->getAuthorEmail(),
                'title' => $parsedTitleAndSummary['title'],
                'summary' => $parsedTitleAndSummary['summary'],
                'repository' => $repositoryName,
                'organization' => $organizationName,
                'hash' => $commit->getHash(),
                'created_at' => $commit->getAuthorDate(),
                'committed_at' => $commit->getAuthorDate(),
            ]);
        }
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
