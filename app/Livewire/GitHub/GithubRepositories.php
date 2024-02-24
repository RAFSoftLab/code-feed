<?php

namespace App\Livewire\GitHub;

use App\Jobs\LoadGitRepositoryJob;
use App\Rules\ValidGithubName;
use App\Rules\ValidGithubURL;
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
    public string $newRepository = '';

    protected function rules(): array
    {
        return ['newRepository' => ['URL', new ValidGithubURL()]];
    }
    protected $messages = [
        'newRepository' => 'The :attribute must be a valid GitHub https url.',
    ];

    public function render(): View
    {
        $user = Auth::user();
        if ($user->isGithubUser()) {

            $accessToken =  $user->github_access_token;
            $response = Http::withToken($accessToken)->get('https://api.github.com/user/repos');
            $this->repositories = array_filter(
                json_decode($response->body(), true),
                fn($repository) => $this->filterImported($repository)
            );

        }

        return view('livewire.github-repositories', [
            'repositories' => $this->repositories,
        ]);
    }

    public function importSelected(): void
    {
        if ($this->newRepository) {
            $this->validateOnly('newRepository');
            $this->selectedRepositories[] = $this->newRepository;
            $this->newRepository = '';
        }

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