<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Services\AI\GoogleAIService;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowCommit extends Component
{

    private string $hash;

    public function mount(string $hash): void
    {
        $this->hash = $hash;
    }

    #[Title('Commit')]
    public function render(GoogleAIService $googleAIService): View
    {
        $commit = Commit::where('hash', $this->hash)->first();
        $explanation = $googleAIService->explain($commit->change);
        return view('livewire.show-commit')
            ->with('commit', $commit)
            ->with('explanation', $explanation);
    }
}
