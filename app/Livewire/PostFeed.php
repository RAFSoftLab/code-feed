<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Models\Post;
use Livewire\Attributes\Title;
use Livewire\Component;

class PostFeed extends Component
{
    public string $organization;
    public string $repository;

    public function mount(string $organization, string $repository): void
    {
        $this->organization = $organization;
        $this->repository = $repository;
    }

    #[Title('CodeFeed')]
    public function render()
    {
        $commits = Commit::where('organization', $this->organization)
            ->where('repository', $this->repository)
            ->with('posts')
            ->with('posts.commit')
            ->get();
        $posts = $commits->pluck('posts')->flatten();

        return view('livewire.post-feed')
            ->with('posts', $posts);
    }
}
