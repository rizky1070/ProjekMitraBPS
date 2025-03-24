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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <title>Input Survei</title>
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

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
        <a href="{{ url('/daftarSurvei') }}"  class="px-4 py-2 bg-orange text-black rounded-bl-none rounded-br-md">
            <
        </a>
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">Input Survei</h2>
            <form action="{{ route('simpanSurvei') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="id_provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select name="id_provinsi" id="id_provinsi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Provinsi</option>
                        <!-- Populate with options from your database -->
                    </select>
                </div>
                <div class="mb-4">
                    <label for="id_kabupaten" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <select name="id_kabupaten" id="id_kabupaten" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Kabupaten</option>
                        <!-- Populate with options from your database -->
                    </select>
                </div>
                <div class="mb-4">
                    <label for="id_kecamatan" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <select name="id_kecamatan" id="id_kecamatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Kecamatan</option>
                        <!-- Populate with options from your database -->
                    </select>
                </div>
                <div class="mb-4">
                    <label for="id_desa" class="block text-sm font-medium text-gray-700">Desa</label>
                    <select name="id_desa" id="id_desa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Desa</option>
                        <!-- Populate with options from your database -->
                    </select>
                </div>
                <div class="mb-4">
                    <label for="nama_survei" class="block text-sm font-medium text-gray-700">Nama Survei</label>
                    <input type="text" name="nama_survei" id="nama_survei" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="lokasi_survei" class="block text-sm font-medium text-gray-700">Lokasi Survei</label>
                    <input type="text" name="lokasi_survei" id="lokasi_survei" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="kro" class="block text-sm font-medium text-gray-700">KRO</label>
                    <input type="text" name="kro" id="kro" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="jadwal_kegiatan" class="block text-sm font-medium text-gray-700">Jadwal Kegiatan</label>
                    <input type="date" name="jadwal_kegiatan" id="jadwal_kegiatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="status_survei" class="block text-sm font-medium text-gray-700">Status Survei</label>
                    <select name="status_survei" id="status_survei" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="1">belum dikerjakan</option>
                        <option value="2">sedang dikerjakan</option>
                        <option value="3">sudah dikerjakan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="tim" class="block text-sm font-medium text-gray-700">Tim</label>
                    <input type="text" name="tim" id="tim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Simpan</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>