<?php
$title = 'Super Tim';
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
            background-color: #ccc; /* default gray */
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

        /* ON */
        input:checked + .slider {
            background-color: #007BFF; /* Bootstrap Blue */
        }

        /* ON handle position */
        input:checked + .slider:before {
            transform: translateX(22px);
        }
    </style>
</head>
<body class="h-full">
    <!-- SweetAlert Logic -->
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
    <!-- component -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="flex justify-between mb-4">
                    <h1 class="text-2xl font-bold mb-4">Super TIM</h1>
                    <a href="/sekretariat" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Sekretariat
                    </a>
                </div>
                <div class="bg-white p-4 rounded shadow">
                <div class="flex justify-between mb-4">
                    <div class="flex space-x-4 items-center">
                        <!-- Search Dropdown with Tom Select -->
                        <div class="w-64">
                            <select id="searchSelect" placeholder="Cari nama..." class="w-full">
                                <option value="">Semua Nama</option>
                                @foreach($officeNames as $name)
                                    <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Category Filter with Tom Select -->
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
                </div>
                @foreach ($offices as $office)
                    <div class="flex items-center justify-between border-2 border-gray-400 rounded-3xl pl-5 pr-2 m-2">
                        <div>
                            <a href="{{ $office->link }}" class="text-xl font-bold transition-all duration-200 hover:text-2xl">
                                {{ $office->name ?? $office->link ?? 'Tidak ada link' }}
                            </a>
                            <p>{{ $office->category->name }}</p>
                        </div>
                    </div>
                @endforeach
                </div>
                <script>
                    function toggleStatus(checkbox) {
                        const officeId = checkbox.getAttribute('data-office-id');
                        const isActive = checkbox.checked;
                        const slider = checkbox.nextElementSibling;
                                    
                                    // Update UI immediately
                        slider.classList.toggle('bg-blue-600', isActive);
                        slider.classList.toggle('bg-gray-400', !isActive);
                                    
                                    // Kirim request AJAX
                        fetch('{{ route("super-tim.update-status") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                office_id: officeId,
                                status: isActive ? 'active' : 'inactive'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                            // Revert changes if failed
                                checkbox.checked = !isActive;
                                slider.classList.toggle('bg-blue-600', !isActive);
                                slider.classList.toggle('bg-gray-400', isActive);
                                swal("Error!", data.message, "error");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            checkbox.checked = !isActive;
                            slider.classList.toggle('bg-blue-600', !isActive);
                            slider.classList.toggle('bg-gray-400', isActive);
                            swal("Error!", "Terjadi kesalahan jaringan", "error");
                        });
                    }
                </script>
                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize Tom Select for search dropdown
                        const searchSelect = new TomSelect('#searchSelect', {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            },
                            placeholder: "Cari nama...",
                            maxOptions: 5,
                        });
                        
                        // Initialize Tom Select for category dropdown
                        const categorySelect = new TomSelect('#categoryFilter', {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            },
                            placeholder: "Pilih kategori...",
                            maxOptions: 5,
                        });
                        
                        function applyFilters() {
                            const params = new URLSearchParams();
                            
                            // Add search parameter if exists and not empty
                            const searchValue = searchSelect.getValue();
                            if (searchValue) {
                                params.append('search', searchValue);
                            }
                            
                            // Add category parameter only if has value and not 'all'
                            const categoryValue = categorySelect.getValue();
                            if (categoryValue && categoryValue !== 'all') {
                                params.append('category', categoryValue);
                            }
                            
                            // Reload page with new query parameters
                            window.location.href = window.location.pathname + '?' + params.toString();
                        }
                        
                        // Event listeners
                        searchSelect.on('change', applyFilters);
                        categorySelect.on('change', applyFilters);
                    });
                </script>
            </main>
        </div>
    </div>
</body>
</html>