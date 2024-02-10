{{-- @formatter:off --}}
@aware(['currentStep'])
@props(['iteration', 'step', 'url'])

@php($passed = $step < $currentStep)
@php($current = (string)$currentStep === (string)$step)

@if ($passed)
    <a href="{{ $url }}">
@endif
<div class="fd-flex fd-flex-col fd-items-center fd-justify-center fd-gap-1">
    <div @class([
      'fd-flex fd-items-center fd-justify-center fd-w-8 fd-h-8 fd-rounded-full fd-p-1',
      'fd-text-zinc-100 fd-bg-zinc-800' => $passed,
      'fd-text-zinc-800 fd-bg-zinc-100' => !$passed && !$current,
      'fd-text-zinc-100 fd-bg-sky-800' => $current,
    ])>
        {{ $iteration }}
    </div>
    <span @class(['fd-text-sm', 'fd-font-medium' => $passed || $current, 'fd-text-zinc-700' => !$current, 'fd-text-sky-800' => $current])>{{ $slot }}</span>
</div>
@if($passed)
    </a>
@endif