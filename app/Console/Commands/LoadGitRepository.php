<?php

namespace App\Console\Commands;

use App\Models\Commit;
use App\Services\AI\OpenAIService;
use Gitonomy\Git\Admin;
use Gitonomy\Git\Diff\FileChange;
use Gitonomy\Git\Tree;
use Illuminate\Console\Command;

class LoadGitRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-git-repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads a git repository from web';

    /**
     * Execute the console command.
     */
    public function handle(OpenAIService $openAIService): void
    {
        $arguments = $this->arguments();
        if (empty($githubRepository))
            $githubRepository = 'https://github.com/RAFSoftLab/code-feed-test-repo.git';
        else
            $githubRepository = $arguments[0];

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
            $commitChanges = $this->getCommitChanges($commit);
            $hasSecurityIssues =false; //$openAIService->findIssues($commit->getHash())['security'];

            Commit::create([
                'author_name' => $commit->getAuthorName(),
                'author_email' => $commit->getAuthorEmail(),
                'title' => $parsedTitleAndSummary['title'],
                'summary' => $parsedTitleAndSummary['summary'],
                'repository' => $repositoryName,
                'organization' => $organizationName,
                'hasSecurityIssues' => $hasSecurityIssues,
                'hash' => $commit->getHash(),
                'created_at' => $commit->getAuthorDate(),
                'committed_at' => $commit->getAuthorDate(),
            ]);
        }
    }

    private function getCommitChanges(\Gitonomy\Git\Commit $commit): string
    {
        $this->displayTree($commit->getTree());
//        $files = $diff->getFiles();
//        foreach ($files as $file) {
//            $changes = $file->getChanges();
//            foreach ($changes as $change) {
//                $lines = $change->getLines();
//                foreach ($lines as $data) {
//                    list ($type, $line) = $data;
//                    if ($type === FileChange::LINE_CONTEXT) {
//                        echo ' '.$line.PHP_EOL;
//                    } elseif ($type === FileChange::LINE_ADD) {
//                        echo '+'.$line.PHP_EOL;
//                    } else {
//                        echo '-'.$line.PHP_EOL;
//                    }
//                }
//            }
//        }


        return '';
    }

    function displayTree(Tree $tree, int $indent = 0): void
    {
        $indent_str = str_repeat(' ', $indent);
        foreach ($tree->getEntries() as $name => $data) {
            list($mode, $entry) = $data;
            if ($entry instanceof Tree) {
                echo $indent_str.$name.'/'.PHP_EOL;
                $this->displayTree($tree, $indent + 1);
            } else {
                echo $indent_str.$name.PHP_EOL;
            }
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
