<div>
    @foreach($orgRepoCombo as $orgRepo)
        <p>
                {{ $orgRepo->organization }}/{{ $orgRepo->repository }}
                <a class="label red" href="/{{ $orgRepo->organization }}/{{ $orgRepo->repository }}/commits">Commit history</a>
                <a class="label blue" href="/{{ $orgRepo->organization }}/{{ $orgRepo->repository.'/post-feed' }}">CodeFeed</a>
        </p>
    @endforeach
</div>
