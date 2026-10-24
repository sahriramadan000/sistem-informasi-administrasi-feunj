@php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    
    if ($lastPage <= 7) {
        $pageNumbers = range(1, $lastPage);
    } else {
        $pageNumbers = [];
        $pageNumbers[] = 1;
        
        if ($currentPage <= 3) {
            // Awal: 1, 2, 3, '...', lastPage
            for ($i = 2; $i <= min(3, $lastPage - 1); $i++) {
                $pageNumbers[] = $i;
            }
            $pageNumbers[] = '...';
            $pageNumbers[] = $lastPage;
        } elseif ($currentPage >= $lastPage - 2) {
            // Akhir: 1, '...', lastPage-2, lastPage-1, lastPage
            $pageNumbers[] = '...';
            for ($i = max(2, $lastPage - 2); $i <= $lastPage; $i++) {
                $pageNumbers[] = $i;
            }
        } else {
            // Tengah: 1, '...', current-1, current, current+1, '...', lastPage
            $pageNumbers[] = '...';
            $pageNumbers[] = $currentPage - 1;
            $pageNumbers[] = $currentPage;
            $pageNumbers[] = $currentPage + 1;
            $pageNumbers[] = '...';
            $pageNumbers[] = $lastPage;
        }
    }
@endphp

<nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3.5 bg-white">
    {{-- Left: Single Clean Result Info (No Duplicate) --}}
    <div>
        <p class="text-xs sm:text-[13px] text-gray-500">
            Menampilkan <strong class="font-bold text-gray-800">{{ $paginator->firstItem() ?? 0 }}</strong> sampai <strong class="font-bold text-gray-800">{{ $paginator->lastItem() ?? 0 }}</strong> dari <strong class="font-bold text-gray-800">{{ number_format($paginator->total(), 0, ',', '.') }}</strong> data
        </p>
    </div>

    {{-- Right: Apple iOS / macOS Compact Segmented Pill Pagination --}}
    @if ($paginator->hasPages())
        <div>
            <div class="inline-flex items-center gap-1 p-1 bg-gray-100/70 rounded-xl border border-gray-200/60 shadow-2xs">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Sebelumnya"
                        class="w-8 h-8 rounded-lg text-gray-300 flex items-center justify-center cursor-not-allowed">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya"
                        class="w-8 h-8 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white flex items-center justify-center transition-all cursor-pointer">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </a>
                @endif

                {{-- Page Links (Compact & Sleek) --}}
                @foreach ($pageNumbers as $page)
                    @if ($page === '...')
                        <span class="w-7 h-8 text-gray-400 text-xs flex items-center justify-center font-bold tracking-widest cursor-default">
                            &hellip;
                        </span>
                    @elseif ($page == $currentPage)
                        <span aria-current="page"
                            class="w-8 h-8 rounded-lg bg-gradient-to-r from-[#FF4D00] to-amber-500 text-white font-bold text-xs flex items-center justify-center shadow-xs cursor-default">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}"
                            class="w-8 h-8 rounded-lg text-gray-700 hover:text-gray-900 hover:bg-white text-xs font-semibold flex items-center justify-center transition-all cursor-pointer"
                            aria-label="Halaman {{ $page }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya"
                        class="w-8 h-8 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white flex items-center justify-center transition-all cursor-pointer">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Berikutnya"
                        class="w-8 h-8 rounded-lg text-gray-300 flex items-center justify-center cursor-not-allowed">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </span>
                @endif
            </div>
        </div>
    @endif
</nav>

@push('scripts')
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush
