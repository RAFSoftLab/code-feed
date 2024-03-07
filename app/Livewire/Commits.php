<?php

namespace App\Livewire;

use App\Models\Repository;
use App\Rules\ValidGithubName;
use App\Services\GithubService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class Commits extends Component
{
    public string $organization;
    public string $repository;

    public function mount(string $organization, string $repository): void
    {
        $this->organization = $organization;
        $this->repository = $repository;
    }

    protected function rules(): array
    {
        return [
            'organization' => new ValidGithubName(),
            'repository' => new ValidGithubName(),
        ];
    }

    #[Title('Feed')]
    public function render(GithubService $service): View
    {
        if (!Gate::allows('access-repository', [$this->organization, $this->repository])) {
            abort(403);
        }

        $this->validate();

        $repository =  Repository::where('user_id', auth()->user()->id)
            ->where('organization', $this->organization)
            ->where('name', $this->repository)
            ->with('commits')
            ->first();

        return view('livewire.commits')
            ->with('commits', $repository->commits)
            ->with('githubService', $service);
    }
}
