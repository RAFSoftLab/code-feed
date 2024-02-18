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
    public Collection $alreadyImportedRepositories;


    public function render(): View
    {
        $user = Auth::user();
        $accessToken =  $user->github_access_token;
        $response = Http::withToken($accessToken)->get('https://api.github.com/user/repos');
        $this->repositories = array_filter(
            json_decode($response->body(), true),
            fn($repository) => $this->filterImported($repository)
        );

        return view('livewire.github-repositories', [
            'repositories' => $this->repositories,
        ]);
    }

    public function importSelected(): void
    {
        // Reset the selected repositories array
        foreach ($this->selectedRepositories as $selectedRepository) {
            LoadGitRepositoryJob::dispatch([
                'repository' => $selectedRepository,
                'user' => Auth::user(),
            ]);
        }
    }

    public function getSelectedRepositoryNamesProperty(): array
    {
        return collect($this->repositories)
            ->whereIn('url', $this->selectedRepositories)
            ->pluck('name')
            ->all();
    }

    function filterImported(array $repository): bool
    {
        foreach ($this->alreadyImportedRepositories as $importedRepository) {
            if ($repository['full_name'] === "$importedRepository->organization/$importedRepository->name") {
                return false;
            }
        }
        return !in_array($repository['clone_url'], $this->selectedRepositories);
    }
}