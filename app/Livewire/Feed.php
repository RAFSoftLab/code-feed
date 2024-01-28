<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Services\GithubService;
use Livewire\Attributes\Title;
use Livewire\Component;

class Feed extends Component
{

    #[Title('Feed')]
    public function render(GithubService $service)
    {
        return view('livewire.feed')
            ->with('posts', Commit::all())
            ->with('githubService', $service);
    }
}
