@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-leading-5 fd-rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-700 fd-bg-white fd-border fd-border-gray-300 fd-leading-5 fd-rounded-md hover:fd-text-gray-500 focus:fd-outline-none focus:fd-ring fd-ring-gray-300 focus:fd-border-blue-300 active:fd-bg-gray-100 active:fd-text-gray-700 fd-transition fd-ease-in-out fd-duration-150">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-700 fd-bg-white fd-border fd-border-gray-300 fd-leading-5 fd-rounded-md hover:fd-text-gray-500 focus:fd-outline-none focus:fd-ring fd-ring-gray-300 focus:fd-border-blue-300 active:fd-bg-gray-100 active:fd-text-gray-700 fd-transition fd-ease-in-out fd-duration-150">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-leading-5 fd-rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
