<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>
        @vite('resources/css/app.css')
        @vite('resources/css/post.css')
    </head>
    <body>
    <x-turbine-ui-container variant="primary" size="full">
        <div class="flex">
            <div class="w-1/5  h-screen">
                <ul>
                    <li><x-turbine-ui-link href="/">CodeFeed</x-turbine-ui-link ></li>
                    <li><x-turbine-ui-link href="/repository-selector">Repositories</x-turbine-ui-link ></li>
                </ul>
            </div>
            <div class="w-4/5">
                {{ $slot }}
            </div>
        </div>
    </x-turbine-ui-container>
    </body>
</html>
