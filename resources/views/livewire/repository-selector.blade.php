<div>
    @foreach($orgRepoCombo as $orgRepo)
        <p>
            {{ $orgRepo->organization }}/{{ $orgRepo->name }}
            <a class="label red" href={{route('commits', ['organization' => $orgRepo->organization, 'repository' => $orgRepo->name])}}>Commit history</a>
            <a class="label blue" href="{{ route('post-feed', ['organization' => $orgRepo->organization, 'repository' => $orgRepo->name]) }}">CodeFeed</a>
        </p>
    @endforeach
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <livewire:git-hub.github-repositories :already-imported="$orgRepoCombo"/>
    </div>
</div>
