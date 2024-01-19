<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use VersionControl_Git;

class PearTest extends TestCase
{
    public function test_load_repository_commits(): void
    {
        $gitRootDir = $this->generateTmpDir();
        $git = new VersionControl_Git($gitRootDir);

        $git->createClone('https://github.com/RAFSoftLab/code-feed.git');
        $hasCommits = count($git->getCommits('master')) > 0;

        $this->assertTrue($hasCommits);
        $this->removeDirectory($gitRootDir.'/code-feed');
    }

    protected function generateTmpDir(): string
    {
        $dirname = sys_get_temp_dir().DIRECTORY_SEPARATOR.'test'.time();
        mkdir($dirname);

        return $dirname;
    }

    protected function removeDirectory($dir): void
    {
        system('rm -rf '.$dir);
    }
}
