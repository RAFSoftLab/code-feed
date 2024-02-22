<div class="flex">
    <!-- Form on the Left -->
    <div class="flex-1">
        <form wire:submit.prevent="importSelected" class="bg-white p-8">
            <div class="mb-4">
                @foreach($repositories as $repository)
                    <label for="repo_{{ $repository['clone_url'] }}" class="block text-gray-700 text-sm font-bold mb-2">
                        <input type="checkbox" id="repo_{{ $repository['clone_url'] }}" wire:model="selectedRepositories" value="{{ $repository['clone_url'] }}" class="mr-2 leading-tight">
                        {{ $repository['full_name'] }}
                    </label>
                @endforeach
            </div>
            <div class="mb-4">
                <x-input-label for="new_repository" class="block text-gray-700 text-sm font-bold mr-2">Import any open source repository:</x-input-label>
                <x-text-input id="new_repository" wire:model="newRepository" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
            </div>
            <x-primary-button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Import Selected Repositories</x-primary-button>
        </form>
    </div>

    <!-- Selected Repositories on the Right -->
    @if(count($selectedRepositories) > 0)
        <div class="flex-1 bg-white p-8">
            <h3 class="text-gray-700 text-sm font-bold mb-2">Selected Repositories:</h3>
            <ul class="list-disc pl-5">
                @foreach($selectedRepositories as $repoName)
                    <li class="text-gray-700 text-sm">{{ $repoName }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>