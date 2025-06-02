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
                <!-- Link -->
                <div class="flex flex-col items-center justify-center bg-white rounded-lg shadow-md">
                    <p class="text-3xl font-bold text-center my-4">
                        Link Tersedia :
                    </p>
                    <div class="flex w-full justify-between">
                        <div class="text-2xl font-bold border rounded-lg shadow-sm m-4 p-4">
                            Link Sekretariat
                            <ul class="block text-lg font-bold list-disc list-inside">
                                <li>Total Link :</li>
                                <li>Link Aktif :</li>
                                <li>Link Nonaktif :</li>
                            </ul>
                        </div>
                        <div class="text-2xl font-bold border rounded-lg shadow-sm m-4 p-4">
                            Link Super Tim
                            <ul class="block text-lg font-bold list-disc list-inside">
                                <li>Total Link :</li>
                                <li>Link Aktif :</li>
                                <li>Link Nonaktif :</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Kategori -->
                <div class="flex flex-col items-center justify-center bg-white rounded-lg shadow-md mt-6">
                    <p class="text-3xl font-bold text-center my-4">
                        Kategori Tersedia :
                    </p>
                    <div class="flex w-full justify-between">
                        <div class="text-2xl font-bold border rounded-lg shadow-sm m-4 p-4">
                            Kategori Sekretariat :
                        </div>
                        <div class="text-2xl font-bold border rounded-lg shadow-sm m-4 p-4">
                            Kategori Super Tim : 
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>
    <!-- Modal Upload Excel -->
</body>
</html>