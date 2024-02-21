<form wire:submit.prevent="importSelected">
    @foreach($repositories as $repository)
        <label for="repo_{{ $repository['clone_url'] }}" class="block">
            <input type="checkbox" id="repo_{{ $repository['clone_url'] }}" wire:model="selectedRepositories" value="{{ $repository['clone_url'] }}">
            {{ $repository['full_name'] }}
        </label>
    @endforeach
    <div class="flex items-center">
        <x-input-label for="new_repository" class="text-lg">Import any open source repository</x-input-label>
        <x-text-input id="new_repository" wire:model="newRepository" />
    </div>
    <br />
    <x-primary-button class="ms-3 mt-2">Import Selected Repositories</x-primary-button>
</form>
@if(count($selectedRepositories) > 0)
    <div class="mt-4 ms-3">
        <h3>Selected Repositories:</h3>
        <ul>
            @foreach($selectedRepositories as $repoName)
                <li>{{ $repoName }}</li>
            @endforeach
        </ul>
    </div>
@endif