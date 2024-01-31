<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Services\GithubService;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class CommitFeed extends Component
{
    public string $organization;
    public string $repository;

    public function mount(string $organization, string $repository): void
    {
        $this->organization = $organization;
        $this->repository = $repository;
    }

    #[Title('Feed')]
    public function render(GithubService $service): View
    {
        $commits = Commit::where('organization', $this->organization)
            ->where('repository', $this->repository)
            ->get();

        return view('livewire.commits')
            ->with('commits', $commits)
            ->with('githubService', $service);
    }
}
