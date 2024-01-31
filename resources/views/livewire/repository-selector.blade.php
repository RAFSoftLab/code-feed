<div>
    @foreach($orgRepoCombo as $orgRepo)
        <p><a wire:navigate href="/commits/{{ $orgRepo->organization }}/{{ $orgRepo->repository }}">{{ $orgRepo->organization }}/{{ $orgRepo->repository }}</a></p>
    @endforeach
</div>
