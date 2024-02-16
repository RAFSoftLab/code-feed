<?php

namespace App\Livewire\GitHub;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class GithubRepositories extends Component
{
    public array $selectedRepositories = [];
    public array $repositories = [];

    public function mount()
    {
        $accessToken = request()->query('access_token');
        $refreshToken = request()->query('refresh_token');
        $response = Http::withToken($accessToken)->get('https://api.github.com/user/repos');
        $this->repositories = json_decode($response->body(), true);
    }

    public function render()
    {
        return view('livewire.github-repositories', [
            'repositories' => $this->repositories,
        ]);
    }

    public function importSelected()
    {
        // Handle the import logic here
        // For now, we'll just output the selected repositories
    }

    public function getSelectedRepositoryNamesProperty()
    {
        return collect($this->repositories)
            ->whereIn('url', $this->selectedRepositories)
            ->pluck('name')
            ->all();
    }
}