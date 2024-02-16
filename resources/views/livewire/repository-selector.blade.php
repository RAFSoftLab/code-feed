<div>
    @foreach($orgRepoCombo as $orgRepo)
        <p>
                {{ $orgRepo->organization }}/{{ $orgRepo->name }}
                <a class="label red" href={{route('commits', ['organization' => $orgRepo->organization, 'repository' => $orgRepo->name])}}>Commit history</a>
                <a class="label blue" href="/{{ $orgRepo->organization }}/{{ $orgRepo->name.'/post-feed' }}">CodeFeed</a>
        </p>
    @endforeach
</div>
