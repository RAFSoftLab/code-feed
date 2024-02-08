<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Services\Feed\FeedService;
use Livewire\Attributes\Title;
use Illuminate\View\View;
use Livewire\Component;

class CodeFeed extends Component
{
    #[Title('CodeFeed')]
    public function render(FeedService $feedService): View
    {
        return view('livewire.code-feed')
            ->with('posts', $feedService->getFeed());
    }
}
