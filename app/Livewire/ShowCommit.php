<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Rules\ValidGithubName;
use App\Services\AI\GoogleAIService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowCommit extends Component
{
    public string $organization;
    public string $repository;
    private string $hash;

    protected function rules(): array
    {
        return [
            'organization' => new ValidGithubName(),
            'repository' => new ValidGithubName(),
        ];
    }


    public function mount(string $organization, string $repository, string $hash): void
    {
        $this->organization = $organization;
        $this->repository = $repository;
        $this->hash = $hash;
    }

    #[Title('Commit')]
    public function render(GoogleAIService $googleAIService): View
    {
        if (!Gate::allows('access-repository', [$this->organization, $this->repository])) {
            abort(403);
        }

        $commit = Commit::where('hash', $this->hash)->first();
        $explanation = $googleAIService->explain($commit->change);
        return view('livewire.show-commit')
            ->with('commit', $commit)
            ->with('explanation', $explanation);
    }
}
