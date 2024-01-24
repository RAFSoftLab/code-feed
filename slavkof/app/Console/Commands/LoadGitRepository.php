<?php

namespace App\Console\Commands;

use App\Models\Commit;
use Illuminate\Console\Command;
use VersionControl_Git;

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
    public function handle()
    {
        $gitRootDir = $this->generateTmpDir();
        $git = new VersionControl_Git($gitRootDir);

        $git->createClone('https://github.com/RAFSoftLab/code-feed.git', false,'code-feed');
        $commits =  $git->getCommits('master');
        $this->removeDirectory($gitRootDir.'/code-feed');

        foreach ($commits as $commit) {
            Commit::create([
                'author' => $commit->getAuthor(),
                'message' => $commit->getMessage(),
                'repository' => 'code-feed',
                'tree' => $commit->getTree(),
                'committer' => $commit->getCommitter(),
                'created_at' => $commit->getCreatedAt(),
                'committed_at' => $commit->getCommittedAt(),
            ]);
        }
    }

    protected function generateTmpDir(): string
    {
        $dirname = sys_get_temp_dir().DIRECTORY_SEPARATOR.'repo'.time();
        mkdir($dirname);

        return $dirname;
    }

    protected function removeDirectory($dir): void
    {
        system('rm -rf '.$dir);
    }
}
