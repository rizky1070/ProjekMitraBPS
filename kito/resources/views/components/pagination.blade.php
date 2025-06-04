@if ($paginator instanceof Illuminate\Pagination\LengthAwarePaginator && $paginator->hasPages())
    <style>
        /* ... (style tetap sama) ... */
    </style>
    <nav aria-label="Page navigation example"
        class="mt-6 py-4 pr-4 sm:pr-8 md:pr-16 flex w-full sm:w-auto rounded-lg {{ $marginX ?? 'mx-2 sm:mx-4' }}">
        <ul class="flex items-center -space-x-px h-8 sm:h-10 text-sm sm:text-base w-full justify-center sm:justify-start">
            <li>
                <a href="{{ $paginator->onFirstPage() ? '#' : $paginator->appends(Arr::except(request()->query(), 'page'))->previousPageUrl() }}" id="prev-btn"
                    class="px-2 sm:px-4 bg-[#D9D9D9] text-black font-semibold h-[3rem] sm:h-[4rem] mx-[0.1rem] sm:mx-[0.3rem] py-2 {{ $paginator->onFirstPage() ? 'hidden' : '' }}">
                    <span class="hidden sm:inline">Previous</span>
                    <span class="sm:hidden">←</span>
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
                <li class="hidden sm:block">
                    <a href="{{ $paginator->appends(Arr::except(request()->query(), 'page'))->url(1) }}"
                        class="px-2 sm:px-4 bg-[#D9D9D9] text-black font-semibold h-[3rem] sm:h-[4rem] mx-[0.1rem] sm:mx-[0.3rem] py-2 visiblePageNum {{ $currentPage == 1 ? 'bg-oren' : '' }}">
                        1
                    </a>
                </li>
                
                @if ($start > 2)
                    <li class="ellipsis text-gray-500 text-xl px-1 sm:px-2 select-none hidden sm:block">• • •</li>
                @endif
            @endif
            
            {{-- Middle pages --}}
            @foreach ($pages as $page)
                @if ($page >= 1 && $page <= $lastPage)
                    <li>
                        <a href="{{ $paginator->appends(Arr::except(request()->query(), 'page'))->url($page) }}"
                            class="px-2 sm:px-4 bg-[#D9D9D9] text-black font-semibold h-[3rem] sm:h-[4rem] mx-[0.1rem] sm:mx-[0.3rem] py-2 visiblePageNum {{ $currentPage == $page ? 'bg-oren' : '' }}">
                            {{ $page }}
                        </a>
                    </li>
                @endif
            @endforeach
            
            {{-- Last page --}}
            @if ($showLastPage && $end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li class="ellipsis text-gray-500 text-xl px-1 sm:px-2 select-none hidden sm:block">• • •</li>
                @endif
                <li class="hidden sm:block">
                    <a href="{{ $paginator->appends(Arr::except(request()->query(), 'page'))->url($lastPage) }}"
                        class="px-2 sm:px-4 bg-[#D9D9D9] text-black font-semibold h-[3rem] sm:h-[4rem] mx-[0.1rem] sm:mx-[0.3rem] py-2 visiblePageNum {{ $currentPage == $lastPage ? 'bg-oren' : '' }}">
                        {{ $lastPage }}
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ $paginator->onLastPage() ? '#' : $paginator->appends(Arr::except(request()->query(), 'page'))->nextPageUrl() }}"
                    class="px-2 sm:px-4 bg-[#D9D9D9] text-black font-semibold h-[3rem] sm:h-[4rem] mx-[0.1rem] sm:mx-[0.3rem] py-2 {{ $paginator->onLastPage() ? 'hidden' : '' }}">
                    <span class="hidden sm:inline">Next</span>
                    <span class="sm:hidden">→</span>
                </a>
            </li>
        </ul>
    </nav>
@endif