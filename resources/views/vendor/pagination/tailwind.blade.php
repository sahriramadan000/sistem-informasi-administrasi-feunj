@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-b-lg">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Next
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                </a>
            @else
                <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
                    Next
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Menampilkan
                    <span class="font-semibold">{{ $paginator->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold">{{ $paginator->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold">{{ $paginator->total() }}</span>
                    hasil
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-lg shadow-sm">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous" class="relative inline-flex items-center px-3 py-2 h-[38px] text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed rounded-l-lg">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 h-[38px] text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all" aria-label="Previous">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true" class="relative inline-flex items-center px-4 py-2 h-[38px] text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative inline-flex items-center px-4 py-2 h-[38px] text-sm font-bold text-white bg-brand border border-brand cursor-default z-10">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 h-[38px] text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all" aria-label="Go to page {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 h-[38px] text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all" aria-label="Next">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next" class="relative inline-flex items-center px-3 py-2 h-[38px] text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed rounded-r-lg">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>

    @push('scripts')
    <script>
        // Re-initialize Lucide icons after pagination loads
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
    @endpush
@endif
