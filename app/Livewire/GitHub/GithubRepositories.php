<?php

namespace App\Livewire\GitHub;

use App\Jobs\LoadGitRepositoryJob;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class GithubRepositories extends Component
{
    public array $selectedRepositories = [];
    public array $repositories = [];
    public Collection $alreadyImported;


    public function render(): View
    {
        $user = Auth::user();
        $accessToken =  $user->github_access_token;
        $response = Http::withToken($accessToken)->get('https://api.github.com/user/repos');
        $this->repositories = json_decode($response->body(), true);

        return view('livewire.github-repositories', [
            'repositories' => $this->repositories,
        ]);
    }

    public function importSelected(): void
    {
        foreach ($this->selectedRepositories as $selectedRepository) {
            LoadGitRepositoryJob::dispatch([
                'repository' => $selectedRepository,
                'user' => Auth::user(),
            ]);
        }
    }

    public function getSelectedRepositoryNamesProperty()
    {
        return collect($this->repositories)
            ->whereIn('url', $this->selectedRepositories)
            ->pluck('name')
            ->all();
    }
}