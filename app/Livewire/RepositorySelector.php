<?php

namespace App\Livewire;

use App\Models\Commit;
use Illuminate\View\View;
use Livewire\Component;

class RepositorySelector extends Component
{
    public function render(): View
    {
        // Get distinct organization repository combo from DB using Eloquent
        $orgRepoCombo = Commit::select('organization', 'repository')->distinct()->get();
        return view('livewire.repository-selector')->with('orgRepoCombo', $orgRepoCombo);
    }
}
