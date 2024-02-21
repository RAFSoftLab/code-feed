<?php

namespace App\Livewire\Dashboard;

use App\Models\Repository;
use Livewire\Component;

class Stats extends Component
{
    public function render()
    {
        // Load all repositories for the loggedin user.
        $repositories = Repository::where('user_id', auth()->id())
            ->withCount('commits')
            ->get();
        // Calculate total and average number of commits for the repositories
        $totalCommits = $repositories->sum(function ($repo) {
            return $repo->commits_count;
        });

        $averageCommits = $repositories->avg(function ($repo) {
            return $repo->commits_count;
        });

        return view('livewire.dashboard.stats')
            ->with("repositoryCount", count($repositories))
            ->with("totalCommits", $totalCommits)
            ->with('averageCommits', $averageCommits);
    }
}
