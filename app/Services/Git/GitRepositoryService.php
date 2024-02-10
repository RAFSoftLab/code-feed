<?php

namespace App\Services\Git;

use Gitonomy\Git\Admin;
use Gitonomy\Git\Repository;

class GitRepositoryService
{
    private string $repositoryUrl;
    private Repository $repository;
    private string $dirName;
    private string $repositoryName;

    public function __construct(string $gitRepositoryUrl)
    {
        $this->repositoryUrl = $gitRepositoryUrl;
        $this->dirName = sys_get_temp_dir().DIRECTORY_SEPARATOR.'repo';
        // pattern for ssh links
        // $pattern = '/:(.*)\/(.*).git/';
        // preg_match($pattern, $githubRepository, $matches);
        preg_match('~github\.com/([^/]+)/([^/.]+?)(?:\.git)?$~', $gitRepositoryUrl, $matches);
        $this->organizationName = $matches[1];
        $this->repositoryName = $matches[2];
    }
    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }
    private string $organizationName;

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }
    public function getDirName(): string
    {
        return $this->dirName;
    }

    public function cloneRepository(): void
    {
        $this->generateTmpDir();
        $this->repository = Admin::cloneTo($this->dirName, $this->repositoryUrl, false);
    }

    public function getCommits(): array
    {
        $log = $this->repository->getLog();

        return  $log->getCommits();
    }


    public function cleanUp(): void
    {
        $this->removeDirectory($this->dirName);
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
}