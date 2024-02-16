<div>
    <form wire:submit.prevent="importSelected">
        @foreach($repositories as $repository)
            <label for="repo_{{ $repository['url'] }}" class="block">
                <input type="checkbox" id="repo_{{ $repository['url'] }}" wire:model="selectedRepositories" value="{{ $repository['url'] }}">
                {{ $repository['name'] }}
            </label>
        @endforeach

        <input type="hidden" name="access_token" value="{{ request()->query('access_token') }}">
        <input type="hidden" name="refresh_token" value="{{ request()->query('refresh_token') }}">

        <button type="submit" class="mt-4">Import Selected Repositories</button>
    </form>

    @if(count($selectedRepositories) > 0)
        <div class="mt-4">
            <h3>Selected Repositories:</h3>
            <ul>
                @foreach($selectedRepositories as $repoName)
                    <li>{{ $repoName }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>