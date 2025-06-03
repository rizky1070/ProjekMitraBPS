<?php
$title = 'Sekretariat';
?>
@include('mitrabps.headerTemp')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #007BFF;
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }
    </style>
</head>
<body class="h-full">
    @if (session('success'))
    <script>
    swal("Success!", "{{ session('success') }}", "success");
    </script>
    @endif

    @if ($errors->any())
    <script>
    swal("Error!", "{{ $errors->first() }}", "error");
    </script>
    @endif
    
    @if (session('error'))
    <script>
    swal("Error!", "{{ session('error') }}", "error");
    </script>
    @endif
    
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="flex justify-between mb-4">
                    <h1 class="text-2xl font-bold mb-4">Sekretariat</h1>
                    <a href="/supertim" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Super Tim
                    </a>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between mb-4">
                        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-2 sm:space-y-0 items-stretch sm:items-center w-full">
                            <div class="w-full md:w-64">
                                <select id="searchSelect" placeholder="Cari nama..." class="w-full">
                                    <option value="">Semua Nama</option>
                                    @foreach($ketuaNames as $name)
                                        <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="w-full md:w-48">
                                <select id="categoryFilter" placeholder="Pilih kategori" class="w-full">
                                    <option value="all">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @if ($ketuas->isEmpty())
                        <div class="text-center text-gray-500 py-8 text-2xl font-bold flex flex-col items-center">
                            Tidak ada link
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mt-2" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" fill="#F3F4F6"/>
                                <circle cx="9" cy="10" r="1.5" fill="#6B7280"/>
                                <circle cx="15" cy="10" r="1.5" fill="#6B7280"/>
                                <path d="M9 16c.5-1 1.5-1.5 3-1.5s2.5.5 3 1.5" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-1 md:grid-cols-2 md:gap-4">
                        @foreach ($ketuas as $ketua)
                            <div class="flex items-center justify-between border-2 border-gray-400 rounded-full px-3 py-1 transition-all duration-200 hover:shadow-lg hover:border-blue-500 bg-white"> 
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="flex-shrink-0 flex items-center justify-center p-1 rounded-full mr-2 transition-colors duration-200 {{ $ketua->priority ? 'bg-red-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $ketua->link }}" class="text-xl font-bold block truncate" title="{{ $ketua->name }}">
                                            {{ $ketua->name ?? $ketua->link ?? 'Tidak ada link' }}
                                        </a>
                                        <p class="truncate">{{ $ketua->category->name }}</p>
                                    </div>
                                </div>
                                <button 
                                    class="bg-blue-600 text-white mr-1 px-2 py-2 rounded-full hover:bg-blue-700 transition flex items-center gap-2"
                                    title="simpan ke pribadi"
                                    x-data="{
                                        loading: false,
                                        success: false,
                                        error: false
                                    }"
                                    @click="
                                        loading = true;
                                        success = false;
                                        error = false;
                                        fetch('{{ route('sekretariat.keep-link', $ketua->id) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            loading = false;
                                            if (data.success) {
                                                success = true;
                                                setTimeout(() => success = false, 3000);
                                            } else {
                                                error = true;
                                                setTimeout(() => error = false, 3000);
                                            }
                                        })
                                        .catch(() => {
                                            loading = false;
                                            error = true;
                                            setTimeout(() => error = false, 3000);
                                        });
                                    "
                                >
                                    <template x-if="loading">
                                        <span class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </template>
                                    
                                    <template x-if="!loading && !success && !error">
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </span>
                                    </template>
                                    <template x-if="success">
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    </template>
                                    
                                    <template x-if="error">
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @include('setape.setapePage', ['paginator' => $ketuas])
                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize Tom Select
                    const searchSelect = new TomSelect('#searchSelect', {
                        create: false,
                        sortField: { field: "text", direction: "asc" },
                        placeholder: "Cari nama...",
                        maxOptions: 5,
                    });
                    
                    const categorySelect = new TomSelect('#categoryFilter', {
                        create: false,
                        sortField: { field: "text", direction: "asc" },
                        placeholder: "Pilih kategori...",
                        maxOptions: 5,
                    });
                    
                    // Filter function
                    function applyFilters() {
                        const params = new URLSearchParams();
                        
                        const searchValue = searchSelect.getValue();
                        if (searchValue) {
                            params.append('search', searchValue);
                        }
                        
                        const categoryValue = categorySelect.getValue();
                        if (categoryValue && categoryValue !== 'all') {
                            params.append('category', categoryValue);
                        }
                        
                        window.location.href = window.location.pathname + '?' + params.toString();
                    }
                    
                    searchSelect.on('change', applyFilters);
                    categorySelect.on('change', applyFilters);
                    
                    // Status toggle functionality
                    document.querySelectorAll('.status-toggle').forEach(toggle => {
                        toggle.addEventListener('change', function() {
                            const ketuaId = this.getAttribute('data-ketua-id');
                            const isActive = this.checked;
                            const slider = this.nextElementSibling;
                            
                            // Update UI immediately
                            slider.classList.toggle('bg-blue-600', isActive);
                            slider.classList.toggle('bg-gray-400', !isActive);
                            
                            // Send AJAX request
                            fetch('/sekretariat/update-status', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    ketua_id: ketuaId,
                                    status: isActive
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    // Revert changes if failed
                                    this.checked = !isActive;
                                    slider.classList.toggle('bg-blue-600', !isActive);
                                    slider.classList.toggle('bg-gray-400', isActive);
                                    swal("Error!", data.message, "error");
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                this.checked = !isActive;
                                slider.classList.toggle('bg-blue-600', !isActive);
                                slider.classList.toggle('bg-gray-400', isActive);
                                swal("Error!", "Terjadi kesalahan jaringan", "error");
                            });
                        });
                    });
                });
                </script>
            </main>
        </div>
    </div>
</body>
</html>