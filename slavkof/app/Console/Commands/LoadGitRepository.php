<?php

namespace App\Console\Commands;

use App\Models\Commit;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use VersionControl_Git;

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
    public function handle()
    {
        $githubRepository = $this->argument('repository');
        $pattern = '/:(.*)\/(.*).git/';
        preg_match($pattern, $githubRepository, $matches);
        $organization =  $matches[1];
        $repository = $matches[2];



        $gitRootDir = $this->generateTmpDir();

        $git = new VersionControl_Git($gitRootDir);
        $git->createClone($githubRepository, false, $repository);
        $commits =  $git->getCommits('master');

        $this->removeDirectory($gitRootDir.$repository);

        Commit::truncate();

        foreach ($commits as $commit) {
            Commit::create([
                'author' => $commit->getAuthor(),
                'message' => $commit->getMessage(),
                'repository' => $repository,
                'organization' => $organization,
                'tree' => $commit,
                'committer' => $commit->getCommitter(),
                'created_at' => $commit->getCreatedAt(),
                'committed_at' => $commit->getCommittedAt(),
            ]);
        }
    }

    protected function generateTmpDir(): string
    {
        $dirname = sys_get_temp_dir().DIRECTORY_SEPARATOR.'repo'.time().DIRECTORY_SEPARATOR;
        mkdir($dirname);

        return $dirname;
    }

    protected function removeDirectory($dir): void
    {
        system('rm -rf '.$dir);
    }
}
