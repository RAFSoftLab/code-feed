<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            @foreach($orgRepoCombo as $orgRepo)
                <p>
                    {{ $orgRepo->organization }}/{{ $orgRepo->name }}
                    <a class="label red" href={{route('commits', ['organization' => $orgRepo->organization, 'repository' => $orgRepo->name])}}>Commit history</a>
                    <a class="label blue" href="{{ route('post-feed', ['organization' => $orgRepo->organization, 'repository' => $orgRepo->name]) }}">CodeFeed</a>
                </p>
            @endforeach
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <livewire:git-hub.github-repositories :already-imported-repositories="$orgRepoCombo"/>
            </div>
        </div>
    </div>
</div>
