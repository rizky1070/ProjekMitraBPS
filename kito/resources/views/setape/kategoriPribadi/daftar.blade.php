<?php
$title = 'Super Tim';
?>
@include('mitrabps.headerTemp')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
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
        <div class="flex justify-end mb-4">
            <a href="/sekretariat" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Tambah Kategori
            </a>
        </div>
        <h1 class="text-xl font-bold mb-4">Ketegori Pribadi</h1>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="text-left p-2 border">Nama</th>
                    <th class="text-left p-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categoryuser as $cat)
                    <tr>
                        <td class="p-2 border">{{ $cat->name }}</td>
                        <td class="p-2 border">action edit dan delete</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>

        </div>
    </div>
    <!-- Modal Upload Excel -->
</body>
</html>