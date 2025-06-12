<?php
$title = 'Dashboard Setape';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <link rel="icon" href="{{ asset('Logo BPS.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    @include('mitrabps.headerTemp')
</head>
<body class="h-full">
    <!-- SweetAlert Notifikasi -->
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

    <!-- Layout Dashboard -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="min-h-screen">
                    <h1 class="text-3xl font-bold mb-4">Dashboard Setape</h1>
                    @if (auth()->user()->is_admin || auth()->user()->is_leader)
                    <!-- Card Statistik -->
                    <div class="bg-white p-4 rounded-lg shadow-md my-2">
                        <h2 class="text-2xl font-semibold mb-2">User</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Total User -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">JUMLAH TOTAL USER</h3>
                                <p class="text-xl font-bold mt-2">{{ $userCount }}</p>
                            </div>
    
                            <!-- Total Admin -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">JUMLAH ADMIN</h3>
                                <p class="text-xl font-bold mt-2">{{ $adminUserCount }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white p-4 rounded-lg shadow-md my-2">
                        <h2 class="text-2xl font-semibold mb-2">Link Kelompok Kerja</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Total Link -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">TOTAL LINK KELOMPOK KERJA</h3>
                                @if (auth()->user()->is_admin || auth()->user()->is_leader)
                                <p class="text-xl font-bold mt-2">{{ $officeCount + $ketuaCount }}</p>
                                <div class="flex justify-between text-sm mt-2 text-gray-500">
                                    <span>AKTIF: <strong class="text-gray-700">{{ $officeActiveCount + $ketuaActiveCount }}</strong></span>
                                    <span>NON AKTIF: <strong class="text-gray-700">{{ $officeNonActiveCount + $ketuaNonActiveCount }}</strong></span>
                                </div>
                                @else
                                <p class="text-xl font-bold mt-2">{{ $officeActiveCount + $ketuaActiveCount }}</p>
                                @endif
                            </div>
                            <!-- Link Super Tim -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">TOTAL LINK SUPER TIM</h3>
                                @if (auth()->user()->is_admin || auth()->user()->is_leader)
                                <p class="text-xl font-bold mt-2">{{ $officeCount }}</p>
                                <div class="flex justify-between text-sm mt-2 text-gray-500">
                                    <span>AKTIF: <strong class="text-gray-700">{{ $officeActiveCount }}</strong></span>
                                    <span>NON AKTIF: <strong class="text-gray-700">{{ $officeNonActiveCount }}</strong></span>
                                </div>
                                @else
                                <p class="text-xl font-bold mt-2">{{ $officeActiveCount }}</p>
                                @endif
                            </div>
        
                            <!-- Link Sekretariat -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">TOTAL LINK SEKRETARIAT</h3>
                                @if (auth()->user()->is_admin || auth()->user()->is_leader)
                                <p class="text-xl font-bold mt-2">{{ $ketuaCount }}</p>
                                <div class="flex justify-between text-sm mt-2 text-gray-500">
                                    <span>AKTIF: <strong class="text-gray-700">{{ $ketuaActiveCount }}</strong></span>
                                    <span>NON AKTIF: <strong class="text-gray-700">{{ $ketuaNonActiveCount }}</strong></span>
                                </div>
                                @else
                                <p class="text-xl font-bold mt-2">{{ $ketuaActiveCount }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow-md my-2">
                        <h2 class="text-2xl font-semibold mb-2">Kategori Kelompok Kerja</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Kategori Kelompok Kerja -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">TOTAL KATEGORI KELOMPOK KERJA</h3>
                                <p class="text-xl font-bold mt-2">{{ $categoryCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow-md my-2">
                        <h2 class="text-2xl font-semibold mb-2">Link Pribadi</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Link Pribadi Tim -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">TOTAL LINK PRIBADI</h3>
                                <p class="text-xl font-bold mt-2">{{ $linkPribadiCount }}</p>
                            </div>
                        </div>
                    </div>
                        
                    <div class="bg-white p-4 rounded-lg shadow-md my-2">
                        <h2 class="text-2xl font-semibold mb-2">Kategori Pribadi</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Kategori Kelompok Kerja -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <h3 class="text-blue-700 text-sm font-semibold">TOTAL KATEGORI PRIBADI</h3>
                                <p class="text-xl font-bold mt-2">{{ $categoryPribadiCount }}</p>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </main>
        </div>
    </div>
</body>
</html>