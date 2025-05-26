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
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between mb-4">
                        <div class="flex space-x-4 items-center">
                            <div class="w-64">
                                <select id="searchSelect" placeholder="Cari nama..." class="w-full">
                                    <option value="">Semua Nama</option>
                                    @foreach($ketuaNames as $name)
                                        <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="w-48">
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
                        <a href="/supertim" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Super Tim
                        </a>
                    </div>
                    <h1 class="text-xl font-bold mb-4">Sekretariat</h1>
                    @foreach ($ketuas as $ketua)
                    <div class="flex items-center justify-between border-2 border-gray-400 rounded-3xl pl-5 pr-2 m-2">
                        <div>
                            <a href="{{ $ketua->link }}" class="text-xl font-bold transition-all duration-200 hover:text-2xl">
                                {{ $ketua->name ?? $ketua->link ?? 'Tidak ada link' }}
                            </a>
                            <p>{{ $ketua->category->name }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
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