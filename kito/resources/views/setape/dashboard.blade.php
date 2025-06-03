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
                <div class="p-6 min-h-screen">
                    <div class="text-2xl font-semibold mb-6">
                        Dashboard
                    </div>
                    <div>
                        <div class="flex flex-wrap gap-6 mb-6">
                            <!-- Jumlah User -->
                            <div class="bg-white p-4 rounded shadow-md border-l-4 border-blue-500 widht-full md:w-1/2 lg:w-1/3">
                                <div class="text-blue-700 text-sm font-semibold">JUMLAH USER</div>
                                <div class="text-3xl font-bold mt-2">32</div>
                            </div>

                            <!-- Jumlah Admin -->
                            <div class="bg-white p-4 rounded shadow-md border-l-4 border-blue-500 widht-full md:w-1/2 lg:w-1/3">
                                <div class="text-blue-700 text-sm font-semibold">JUMLAH ADMIN</div>
                                <div class="text-3xl font-bold mt-2">17</div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-6 mb-6">
                            <!-- Link Super -->
                            <div class="bg-white p-4 rounded shadow-md border-l-4 border-blue-500 widht-full md:w-1/2 lg:w-1/3">
                                <div class="text-blue-700 text-sm font-semibold">LINK SUPER TIM</div>
                                <div class="text-3xl font-bold mt-2">29</div>
                                <div class="text-sm mt-1 text-gray-500">AKTIF: <span class="font-bold text-gray-700">39</span></div>
                                <div class="text-sm mt-1 text-gray-500">NON AKTIF: <span class="font-bold text-gray-700">39</span></div>
                            </div>
    
                            <!-- Link Sekretariat -->
                            <div class="bg-white p-4 rounded shadow-md border-l-4 border-blue-500 widht-full md:w-1/2 lg:w-1/3">
                                <div class="text-blue-700 text-sm font-semibold">LINK SEKRETARIAT</div>
                                <div class="text-3xl font-bold mt-2">3</div>
                                <div class="text-sm mt-1 text-gray-500">AKTIF: <span class="font-bold text-gray-700">11</span></div>
                                <div class="text-sm mt-1 text-gray-500">NON AKTIF: <span class="font-bold text-gray-700">11</span></div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-6 mb-6">
                            <!-- Kategori Super -->
                            <div class="bg-white p-4 rounded shadow-md border-l-4 border-blue-500 widht-full md:w-1/2 lg:w-1/3">
                                <div class="text-blue-700 text-sm font-semibold">KATEGORI SUPER TIM AKTIF</div>
                                <div class="text-3xl font-bold mt-2">29</div>
                            </div>
    
                            <!-- Kategori Sekretariat -->
                            <div class="bg-white p-4 rounded shadow-md border-l-4 border-blue-500 widht-full md:w-1/2 lg:w-1/3">
                                <div class="text-blue-700 text-sm font-semibold">KATEGORI SEKRETARIAT</div>
                                <div class="text-3xl font-bold mt-2">3</div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>
    <!-- Modal Upload Excel -->
</body>
</html>