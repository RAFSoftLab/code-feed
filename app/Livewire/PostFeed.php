<?php

namespace App\Livewire;

use App\Models\Commit;
use App\Models\Repository;
use App\Rules\ValidGithubName;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

use Livewire\Attributes\Title;
use Livewire\Component;

class PostFeed extends Component
{
    public string $organization;
    public string $repository;

    protected function rules(): array
    {
        return [
            'organization' => new ValidGithubName(),
            'repository' => new ValidGithubName(),
        ];
    }

    public function mount(string $organization, string $repository): void
    {

        $this->organization = $organization;
        $this->repository = $repository;
    }

    #[Title('Repository Post Feed')]
    public function render(): View
    {
        if (!Gate::allows('access-repository', [$this->organization, $this->repository])) {
            abort(403);
        }

        $this->validate();

        $repository =  Repository::where('user_id', auth()->user()->id)
            ->where('organization', $this->organization)
            ->where('name', $this->repository)
            ->with([
                'commits'
                    => [
                        'posts'
                    ]
            ])
            ->first();
        $posts = $repository->commits->pluck('posts')->flatten();

        return view('livewire.code-feed')
            ->with('posts', $posts);
    }
}
