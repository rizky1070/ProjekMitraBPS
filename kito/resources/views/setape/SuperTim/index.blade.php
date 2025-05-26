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
        
        <a href="/sekretariat" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Sekretariat
        </a>
    </div>
    
    <h1 class="text-xl font-bold mb-4">Super TIM</h1>
    
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
                <tr class="bg-gray-100">
                    <th class="text-left p-2 border">Nama</th>
                    <th class="text-left p-2 border">Kategori</th>
                    <th class="text-left p-2 border">Link</th>
                    <th class="text-left p-2 border">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($offices as $office)
                    <tr>
                        <td class="p-2 border">{{ $office->name }}</td>
                        <td class="p-2 border">{{ $office->category->name ?? '-' }}</td>
                        <td class="p-2 border">
                            <a href="{{ $office->link }}" class="text-blue-500 underline" target="_blank">Lihat</a>
                        </td>
                        <td class="p-2 border">
                            @if ($office->status)
                                <span class="text-green-600 font-semibold">Aktif</span>
                            @else
                                <span class="text-red-600 font-semibold">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>

    </table>
</div>

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