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
<body class="h-full bg-gray-200">
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
    <a href="{{ url('/daftarSurvei') }}" class="px-4 py-2 bg-orange text-black rounded-bl-none rounded-br-md">
        <
    </a>

    <main class="max-w-4xl mx-auto bg-gray-200">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold mb-4">Input Survei</h2>
                <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Import Survei</button>
            </div>
            
            <form action="{{ route('simpanSurvei') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="id_provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select name="id_provinsi" id="id_provinsi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinsi as $prov)
                        <option value="{{ $prov->id_provinsi }}" {{ old('id_provinsi') == $prov->id_provinsi ? 'selected' : '' }}>
                            {{ $prov->nama_provinsi }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="id_kabupaten" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <select name="id_kabupaten" id="id_kabupaten" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Kabupaten</option>
                        @foreach($kabupaten as $kab)
                        <option value="{{ $kab->id_kabupaten }}" {{ old('id_kabupaten') == $kab->id_kabupaten ? 'selected' : '' }}>
                            {{ $kab->nama_kabupaten }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="id_kecamatan" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <select name="id_kecamatan" id="id_kecamatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Kecamatan</option>
                        @foreach($kecamatan as $kec)
                        <option value="{{ $kec->id_kecamatan }}" {{ old('id_kecamatan') == $kec->id_kecamatan ? 'selected' : '' }}>
                            {{ $kec->nama_kecamatan }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="id_desa" class="block text-sm font-medium text-gray-700">Desa</label>
                    <select name="id_desa" id="id_desa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Desa</option>
                        @foreach($desa as $des)
                        <option value="{{ $des->id_desa }}" {{ old('id_desa') == $des->id_desa ? 'selected' : '' }}>
                            {{ $des->nama_desa }}
                        </option>
                        @endforeach
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
                    <button type="submit" class="px-4 py-2 bg-orange text-black rounded-md">Simpan</button>
                </div>
            </form>
        </div>
    </main>
    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-2">Import Survei</h2>
            <p class="mb-2 text-red-700 text-sm">Pastikan format file excel yang diimport sesuai.</p>
            <form action="{{ route('upload.excelSurvei') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                    <a href="{{ asset('addSurvey.xlsx') }}" class="py-2 text-blue-500 hover:text-blue-600 text-xs">
                        Belum punya file excel? Download template disini.
                    </a>
                <div class="flex justify-end mt-4">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md mr-2" onclick="closeModal()">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange text-black rounded-md">Unggah</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }
    </script>
</body>
</html>