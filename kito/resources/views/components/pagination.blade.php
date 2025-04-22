@if ($paginator instanceof Illuminate\Pagination\LengthAwarePaginator && $paginator->hasPages())
    <style>
        /* ... (style tetap sama) ... */
    </style>
    <nav aria-label="Page navigation example"
        class="mt-6 py-4 pr-16 flex w-min-[96] rounded-lg {{ $marginX ?? 'mx-4' }}">
        <ul class="flex items-center -space-x-px h-10 text-base">
            <li>
                <a href="{{ $paginator->onFirstPage() ? '#' : $paginator->appends(Arr::except(request()->query(), 'page'))->previousPageUrl() }}" id="prev-btn"
                    class="px-4 bg-[#D9D9D9] text-black font-semibold h-[4rem] mx-[0.3rem] py-2 {{ $paginator->onFirstPage() ? 'hidden' : '' }}">
                    Previous
                </a>
            </li>
            
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $window = 1; // Ubah dari 2 menjadi 1 untuk data sedikit
                
                $showFirstPage = $lastPage > 1;
                $showLastPage = $lastPage > 1;
                
                // Tentukan range halaman yang akan ditampilkan
                $start = max(1, $currentPage - $window);
                $end = min($lastPage, $currentPage + $window);
                
                // Pastikan tidak ada duplikasi
                $pages = range($start, $end);
                $pages = array_unique($pages);
            @endphp
            
            {{-- First page --}}
            @if ($showFirstPage && $start > 1)
                <li>
                    <a href="{{ $paginator->appends(Arr::except(request()->query(), 'page'))->url(1) }}"
                        class="px-4 bg-[#D9D9D9] text-black font-semibold h-[4rem] mx-[0.3rem] py-2 visiblePageNum {{ $currentPage == 1 ? 'bg-orange' : '' }}">
                        1
                    </a>
                </li>
                
                @if ($start > 2)
                    <li class="ellipsis">...</li>
                @endif
            @endif
            
            {{-- Middle pages --}}
            @foreach ($pages as $page)
                @if ($page >= 1 && $page <= $lastPage)
                    <li>
                        <a href="{{ $paginator->appends(Arr::except(request()->query(), 'page'))->url($page) }}"
                            class="px-4 bg-[#D9D9D9] text-black font-semibold h-[4rem] mx-[0.3rem] py-2 visiblePageNum {{ $currentPage == $page ? 'bg-orange' : '' }}">
                            {{ $page }}
                        </a>
                    </li>
                @endif
            @endforeach
            
            {{-- Last page --}}
            @if ($showLastPage && $end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li class="ellipsis">...</li>
                @endif
                <li>
                    <a href="{{ $paginator->appends(Arr::except(request()->query(), 'page'))->url($lastPage) }}"
                        class="px-4 bg-[#D9D9D9] text-black font-semibold h-[4rem] mx-[0.3rem] py-2 visiblePageNum {{ $currentPage == $lastPage ? 'bg-orange' : '' }}">
                        {{ $lastPage }}
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ $paginator->onLastPage() ? '#' : $paginator->appends(Arr::except(request()->query(), 'page'))->nextPageUrl() }}"
                    class="px-4 bg-[#D9D9D9] text-black font-semibold h-[4rem] mx-[0.3rem] py-2 {{ $paginator->onLastPage() ? 'hidden' : '' }}">
                    Next
                </a>
            </li>
        </ul>
    </nav>
@endif