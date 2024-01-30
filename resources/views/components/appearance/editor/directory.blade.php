@props([
    'requestedFile',
    'directory',
    'recursiveDirectory',
    'files',
])

@php($recursiveDirectory ??= $directory)
@php($isDirectory = is_array($files) || $files instanceof \Illuminate\Contracts\Support\Arrayable)

@if ($isDirectory)
    <x-weblebby::collapsible
            class="fd-space-y-1"
            data-relative="{{ $recursiveDirectory }}"
            :expanded="$directory === '' || str_starts_with($requestedFile->getRelativePathname(), $recursiveDirectory)"
    >
        @if ($directory !== '')
            <button
                    class="fd-w-full fd-flex fd-items-center fd-gap-2 fd-p-1 fd-text-sm fd-rounded fd-text-zinc-400 hover:fd-bg-[#323232] fd-transition-colors"
                    data-collapse-toggle
            >
                <x-weblebby::icons.caret-right-fill
                        class="fd-w-4 fd-h-4 fd-transition-transform"
                        data-collapse-icon
                />
                <span>{{ $directory }}</span>
            </button>
        @endif
        <x-weblebby::collapsible.content class="fd-space-y-1 {{ $directory !== '' ? 'fd-ms-4' : '' }}">
            @foreach ($files as $directory => $childFiles)
                <x-weblebby::appearance.editor.directory
                        :directory="$directory"
                        :recursiveDirectory="sprintf('%s/%s', $recursiveDirectory, $directory)"
                        :files="$childFiles"
                        :requestedFile="$requestedFile"
                />
            @endforeach
        </x-weblebby::collapsible.content>
    </x-weblebby::collapsible>
@else
    <a
            href="{{ panel_route('appearance.editor.index', ['file' => $files->getRelativePathname()]) }}"
            class="fd-w-full fd-flex fd-items-center fd-gap-2 fd-p-1 fd-text-sm fd-rounded fd-text-zinc-200 hover:fd-bg-[#323232] fd-transition-colors {{ $requestedFile->getRelativePathname() === $files->getRelativePathname() ? 'fd-bg-[#323232]' : '' }}"
    >
        <span>{{ $files->getFilename() }}</span>
    </a>
@endif