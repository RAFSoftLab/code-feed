<?php

namespace App\Livewire;

use App\Models\Commit;
use Livewire\Attributes\Title;
use Livewire\Component;
use VersionControl_Git;

class Feed extends Component
{

    #[Title('Feed')]
    public function render()
    {
        return view('livewire.feed')
            ->with('commits', Commit::all());
    }
}
