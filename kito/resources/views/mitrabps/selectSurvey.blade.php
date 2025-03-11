<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Input Mitra BPS</title>
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
        <!-- component -->
        <div x-data="{ sidebarOpen: false }" class="flex h-screen">
            <x-sidebar></x-sidebar>
            <div class="flex flex-col flex-1 overflow-hidden">
                <x-navbar></x-navbar>
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-4">Survei Terpilih</h2>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <p><strong>Nama Survei :</strong> Plan Valencia</p>
                        <p><strong>Lokasi :</strong> Kec. Trowulan</p>
                        <p><strong>Jadwal :</strong> 29 Feb 2099</p>
                        <p><strong>Tim :</strong> Pertanian</p>
                        <p><strong>KRO :</strong> <span class="font-bold">Statistik Peternakan, Perikanan dan Kehutanan</span></p>
                    </div>

                    <h3 class="text-xl font-bold mt-6 mb-4">Daftar Mitra</h3>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center mb-4">
                            <input type="text" placeholder="Search..." class="border p-2 rounded w-full">
                            <button class="bg-gray-300 text-gray-700 px-4 py-2 ml-2 rounded">Filter</button>
                            <button class="bg-orange text-white px-4 py-2 ml-4 rounded">Import excel</button>
                        </div>

                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-2">Nama Mitra</th>
                                    <th class="border border-gray-300 p-2">Domisili</th>
                                    <th class="border border-gray-300 p-2">Survei yang Diikuti</th>
                                    <th class="border border-gray-300 p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 p-2">Si Fulan</td>
                                    <td class="border border-gray-300 p-2">Kec. Trowulan</td>
                                    <td class="border border-gray-300 p-2 text-center">2</td>
                                    <td class="border border-gray-300 p-2 text-center">
                                        <button class="bg-orange text-white px-3 py-1 rounded">Batal</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 p-2">Fulana</td>
                                    <td class="border border-gray-300 p-2">Kec. Trowulan</td>
                                    <td class="border border-gray-300 p-2 text-center">1</td>
                                    <td class="border border-gray-300 p-2 text-center">
                                        <button class="bg-orange text-white px-3 py-1 rounded">Pilih</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 p-2">Fulani</td>
                                    <td class="border border-gray-300 p-2">Kec. Trowulan</td>
                                    <td class="border border-gray-300 p-2 text-center">3</td>
                                    <td class="border border-gray-300 p-2 text-center">
                                        <button class="bg-orange text-white px-3 py-1 rounded">Pilih</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="flex justify-between items-center mt-4">
                            <div>
                                <button class="bg-orange text-white px-4 py-2 rounded">Tambah</button>
                            </div>
                            <div class="flex space-x-1">
                                <button class="bg-orange text-white px-3 py-1 rounded">1</button>
                                <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded">2</button>
                                <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded">3</button>
                                <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded">4</button>
                                <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded">5</button>
                            </div>
                        </div>
                    </div>
                </div>

                </main>
            </div>
        </div>
</body>
</html>