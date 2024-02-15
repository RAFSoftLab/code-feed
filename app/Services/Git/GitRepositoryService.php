<?php

namespace App\Services\Git;

use App\Models\User;
use Gitonomy\Git\Admin;
use Gitonomy\Git\Repository;

class GitRepositoryService
{
    private string $repositoryUrl;
    private Repository $repository;
    private string $dirName;
    private string $repositoryName;
    private ?User $user;
    private string $organizationName;

    public function __construct(string $gitRepositoryUrl, User $user = null)
    {
        $this->repositoryUrl = $gitRepositoryUrl;
        $this->user = $user;
        // pattern for ssh links
        // $pattern = '/:(.*)\/(.*).git/';
        // preg_match($pattern, $githubRepository, $matches);
        preg_match('~github\.com/([^/]+)/([^/.]+?)(?:\.git)?$~', $gitRepositoryUrl, $matches);
        $this->organizationName = $matches[1];
        $this->repositoryName = $matches[2];
        $this->dirName = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            .($user? $user->email.DIRECTORY_SEPARATOR : '')
            .$this->organizationName.'_'.$this->repositoryName;
    }

    private function cloneRepository(): void
    {
        if (!file_exists($this->dirName)) {
            echo "creating new repository in ".$this->dirName."\n";
            Admin::cloneTo($this->dirName, $this->repositoryUrl, false);
        }
        $this->repository = new Repository($this->dirName);
    }

    public function getCommits(): array
    {
        $this->cloneRepository();
        $log = $this->repository->getLog();

        return  $log->getCommits();
    }

    public function getNewCommits(): array
    {
        $this->cloneRepository();
        try {
            $this->repository->run('fetch', ['origin']);
        } catch (\Exception $e) {
            echo "An error occurred while fetching changes: " . $e->getMessage() . "\n";
            exit;
        }

        // Get the current branch name
        $defaultBranchName = 'master';
        try {
            if (!$this->repository->getReferences()->hasBranch($defaultBranchName)) {
                // If 'master' branch does not exist, try to determine the default branch from remote
                $remoteDefaultBranch = $this->repository->run('symbolic-ref', ['refs/remotes/origin/HEAD']);
                $defaultBranchName = basename(trim($remoteDefaultBranch)); // Removes 'refs/remotes/origin/' and trims whitespace
            }
        } catch (\Exception $e) {
            echo "An error occurred while determining the default branch: " . $e->getMessage() . "\n";
            exit;
        }
        $branchName = $defaultBranchName;
        $remoteBranchName = "origin/{$branchName}";

        // Get the commit log difference between the local and remote branches
        try {
            $localCommitHash = $this->repository->getReferences()->getBranch($branchName)->getCommit()->getHash();
            $remoteCommitHash = $this->repository->getReferences()->getRemoteBranch($remoteBranchName)->getCommit()->getHash();
            $localDifferences = $this->getLogBetweenCommits($localCommitHash, $remoteCommitHash);

            $newCommits = [];
            foreach ($localDifferences as $commit) {
                $newCommits[] = $this->repository->getCommit($commit);
            }

            try {
                $this->repository->run('pull');
            } catch (\Exception $e) {
                echo "An error occurred while fetching changes: " . $e->getMessage() . "\n";
                exit;
            }

            return $newCommits;
        } catch (\Exception $e) {
            echo "An error occurred while trying to find differences: " . $e->getMessage() . "\n";
            return array();
        }
    }

    public function cleanUp(): void
    {
        $this->removeDirectory($this->dirName);
    }

    private function getLogBetweenCommits(string $localCommitHash, string $remoteCommitHash): array
    {
        $command = "cd $this->dirName && git --no-pager log --format=\"%H\" $localCommitHash..$remoteCommitHash";
        print "Running command: $command\n";
        exec($command, $output);
        return $output;
    }

    public function getCommitChanges(string $commitHash): string
    {
        exec("cd $this->dirName && git --no-pager show $commitHash", $output);
        return implode("\n", $output);
    }

    private function generateTmpDir(): void
    {
        mkdir($this->dirName);
    }

    private function removeDirectory(string $dir): void
    {
        system('rm -rf '.$dir);
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }
}