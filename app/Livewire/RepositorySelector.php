<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Models\Repository;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class RepositorySelector extends Component
{
    #[Title('Repositories')]
    public function render(): View
    {
        // Get distinct organization repository combo from DB using Eloquent
        $orgRepoCombo = Repository::where('user_id', auth()->user()->id)
            ->get();

        return view('livewire.repository-selector')
            ->with('orgRepoCombo', $orgRepoCombo);
    }
}
