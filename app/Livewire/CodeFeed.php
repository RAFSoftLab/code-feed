<?php

namespace App\Livewire;

use App\Models\Commit;
use Livewire\Attributes\Title;
use Illuminate\View\View;
use Livewire\Component;

class CodeFeed extends Component
{
    #[Title('CodeFeed')]
    public function render(): View
    {
        $commits = Commit::with('posts')
            ->with('posts.commit')
            ->get();
        $posts = $commits->pluck('posts')->flatten();

        return view('livewire.code-feed')
            ->with('posts', $posts);
    }
}
