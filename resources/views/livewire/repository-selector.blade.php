<div>
    @foreach($orgRepoCombo as $orgRepo)
        <p>
                {{ $orgRepo->organization }}/{{ $orgRepo->name }}
                <a class="label red" href="/{{ $orgRepo->organization }}/{{ $orgRepo->name }}/commits">Commit history</a>
                <a class="label blue" href="/{{ $orgRepo->organization }}/{{ $orgRepo->name.'/post-Feed' }}">CodeFeed</a>
        </p>
    @endforeach
</div>
