<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use VersionControl_Git;

class Feed extends Component
{

    #[Title('Feed')]
    public function render()
    {
        return view('livewire.feed')
            ->with('commits', $this->getCommits());
    }

    private function getCommits(): array
    {
        $gitRootDir = $this->generateTmpDir();
        $git = new VersionControl_Git($gitRootDir);

        $git->createClone('https://github.com/RAFSoftLab/code-feed.git');
        $commits =  $git->getCommits('master');
        $this->removeDirectory($gitRootDir.'/code-feed');

        return $commits;
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
