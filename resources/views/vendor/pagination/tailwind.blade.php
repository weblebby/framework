@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="fd-flex fd-items-center fd-justify-between">
        <div class="fd-flex fd-justify-between fd-flex-1 sm:fd-hidden">
            @if ($paginator->onFirstPage())
                <span class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-leading-5 fd-rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-700 fd-bg-white fd-border fd-border-gray-300 fd-leading-5 fd-rounded-md hover:fd-text-gray-500 focus:fd-outline-none focus:fd-ring fd-ring-gray-300 focus:fd-border-blue-300 active:fd-bg-gray-100 active:fd-text-gray-700 fd-transition fd-ease-in-out fd-duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-ml-3 fd-text-sm fd-font-medium fd-text-gray-700 fd-bg-white fd-border fd-border-gray-300 fd-leading-5 fd-rounded-md hover:fd-text-gray-500 focus:fd-outline-none focus:fd-ring fd-ring-gray-300 focus:fd-border-blue-300 active:fd-bg-gray-100 active:fd-text-gray-700 fd-transition fd-ease-in-out fd-duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd-ml-3 fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-leading-5 fd-rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="fd-hidden sm:fd-flex-1 sm:fd-flex sm:fd-items-center sm:fd-justify-between">
            <div>
                <span class="fd-relative fd-z-0 fd-inline-flex fd-shadow-sm fd-rounded-md">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="fd-relative fd-inline-flex fd-items-center fd-px-2 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-rounded-l-md fd-leading-5" aria-hidden="true">
                                <svg class="fd-w-5 fd-h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="fd-relative fd-inline-flex fd-items-center fd-px-2 fd-py-2 fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-rounded-l-md fd-leading-5 hover:fd-text-gray-400 focus:fd-z-10 focus:fd-outline-none focus:fd-ring fd-ring-gray-300 focus:fd-border-blue-300 active:fd-bg-gray-100 active:fd-text-gray-500 fd-transition fd-ease-in-out fd-duration-150" aria-label="{{ __('pagination.previous') }}">
                            <svg class="fd-w-5 fd-h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 -fd-ml-px fd-text-sm fd-font-medium fd-text-gray-700 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 fd--ml-px fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="fd-relative fd-inline-flex fd-items-center fd-px-4 fd-py-2 -fd-ml-px fd-text-sm fd-font-medium fd-text-gray-700 fd-bg-white fd-border fd-border-gray-300 fd-leading-5 hover:fd-text-gray-500 focus:fd-z-10 focus:fd-outline-none focus:fd-ring fd-ring-gray-300 focus:fd-border-blue-300 active:fd-bg-gray-100 active:fd-text-gray-700 fd-transition fd-ease-in-out fd-duration-150" aria-label="{{ __('Sayfaya git: :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="fd-relative fd-inline-flex fd-items-center fd-px-2 fd-py-2 -fd-ml-px fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-rounded-r-md fd-leading-5 hover:fd-text-gray-400 focus:fd-z-10 focus:fd-outline-none focus:fd-ring fd-ring-gray-300 focus:fd-border-blue-300 active:fd-bg-gray-100 active:fd-text-gray-500 fd-transition fd-ease-in-out fd-duration-150" aria-label="{{ __('pagination.next') }}">
                            <svg class="fd-w-5 fd-h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="fd-relative fd-inline-flex fd-items-center fd-px-2 fd-py-2 -fd-ml-px fd-text-sm fd-font-medium fd-text-gray-500 fd-bg-white fd-border fd-border-gray-300 fd-cursor-default fd-rounded-r-md fd-leading-5" aria-hidden="true">
                                <svg class="fd-w-5 fd-h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
