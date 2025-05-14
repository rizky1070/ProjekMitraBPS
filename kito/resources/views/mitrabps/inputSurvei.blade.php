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

    @if (session('info'))
    <script>
    swal("Info!", "{{ session('info') }}", "info");
    </script>
    @endif
    <a href="{{ url('/daftarSurvei') }}" 
    class="inline-flex items-center gap-2 px-4 py-2 bg-orange hover:bg-orange-600 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <main class="max-w-4xl mx-auto bg-gray-200">
        <div class="p-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold mb-4">Input Survei</h2>
                    <!-- Pesan Error -->
                    @if(isset($errors))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Import Survei</button>
                </div>
                <form action="{{ route('simpanSurvei') }}" method="POST">
                    @csrf
                    <div class="flex flex-wrap -mx-3">
                        <!-- Kolom Kiri -->
                        <div class="w-full md:w-1/2 px-3">
                            
                            <div class="mb-5">
                                <label for="nama_survei" class="block text-sm font-medium text-gray-700 mb-1">Nama Survei</label>
                                <input type="text" name="nama_survei" id="nama_survei" value="{{ old('nama_survei') }}" class="text-sm w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50" placeholder="Nama Survei">
                            </div>
                            
                            <div class="mb-5">
                                <label for="id_kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                                <select name="id_kecamatan" id="id_kecamatan" class="text-gray-500 w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($kecamatan as $kec)
                                    <option value="{{ $kec->id_kecamatan }}" {{ old('id_kecamatan') == $kec->id_kecamatan ? 'selected' : '' }}>
                                        [{{ $kec->kode_kecamatan }}] {{ $kec->nama_kecamatan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-5">
                                <label for="id_desa" class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
                                <select name="id_desa" id="id_desa" class="text-gray-500 w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <option value="">Pilih Desa</option>
                                    @foreach($desa as $des)
                                    <option value="{{ $des->id_desa }}" {{ old('id_desa') == $des->id_desa ? 'selected' : '' }}>
                                        [{{ $des->kode_desa }}] {{ $des->nama_desa }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-5">
                                <label for="lokasi_survei" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Survei</label>
                                <input type="text" name="lokasi_survei" id="lokasi_survei" value="{{ old('lokasi_survei') }}" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm" placeholder="Lokasi Survei">
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan -->
                        <div class="w-full md:w-1/2 px-3">
                            
                            <div class="mb-5">
                                <label for="kro" class="block text-sm font-medium text-gray-700 mb-1">KRO</label>
                                <input type="text" name="kro" id="kro" value="{{ old('kro') }}" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm" placeholder="KRO">
                            </div>
                            
                            <div class="mb-5">
                                <label for="tim" class="block text-sm font-medium text-gray-700 mb-1">Tim</label>
                                <input type="text" name="tim" id="tim" value="{{ old('tim') }}" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm" placeholder="Tim">
                            </div>
                            
                            <div class="mb-5">
                                <label for="jadwal_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Jadwal Kegiatan</label>
                                <input type="date" name="jadwal_kegiatan" id="jadwal_kegiatan" value="{{ old('jadwal_kegiatan') }}" class="w-full h-10 text-gray-500 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            </div>

                            <div class="mb-5">
                                <label for="jadwal_berakhir_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Jadwal Berakhir Kegiatan</label>
                                <input type="date" name="jadwal_berakhir_kegiatan" id="jadwal_berakhir_kegiatan" value="{{ old('jadwal_berakhir_kegiatan') }}" class="w-full h-10 text-gray-500 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="px-6 py-2 bg-orange text-black rounded-md hover:bg-orange-600 transition duration-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden" style="z-index: 50;">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-2">Import Survei</h2>
            <p class="mb-2 text-red-700 text-m font-bold">Pastikan format file excel yang diimport sesuai!</p>
            <form action="{{ route('upload.excelSurvei') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                    <p class="py-2 text-s">Belum punya file excel?  
                        <a href="{{ asset('addSurvey.xlsx') }} " class=" text-blue-500 hover:text-blue-600 font-bold">
                            Download template disini.
                        </a>
                    </p>
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
    <!-- JavaScript Tom Select -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <!-- Inisialisasi Tom Select -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kecamatanSelect = new TomSelect('#id_kecamatan', {
                placeholder: 'Pilih Kecamatan',
                searchField: 'text',
                onChange: function(value) {
                    if (value) {
                        fetchDesa(value);
                    } else {
                        resetSelect('id_desa');
                    }
                }
            });
        
            const desaSelect = new TomSelect('#id_desa', {
                placeholder: 'Pilih Desa',
                searchField: 'text'
            });
        
            function fetchKabupaten(id_provinsi) {
                fetch(`/get-kabupaten/${id_provinsi}`)
                    .then(response => response.json())
                    .then(data => {
                        const kabupatenSelect = document.getElementById('id_kabupaten');
                        kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                        
                        data.forEach(kabupaten => {
                            const option = document.createElement('option');
                            option.value = kabupaten.id_kabupaten;
                            option.textContent = kabupaten.nama_kabupaten;
                            kabupatenSelect.appendChild(option);
                        });
                        
                        // Refresh Tom Select instance
                        kabupatenSelect.tomselect.clear();
                        kabupatenSelect.tomselect.clearOptions();
                        kabupatenSelect.tomselect.addOptions(data.map(kab => ({
                            value: kab.id_kabupaten,
                            text: kab.nama_kabupaten
                        })));
                        
                        // Reset dependent selects
                        resetSelect('id_kecamatan');
                        resetSelect('id_desa');
                    });
            }
        
            function fetchKecamatan(id_kabupaten) {
                fetch(`/get-kecamatan/${id_kabupaten}`)
                    .then(response => response.json())
                    .then(data => {
                        const kecamatanSelect = document.getElementById('id_kecamatan');
                        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                        
                        data.forEach(kecamatan => {
                            const option = document.createElement('option');
                            option.value = kecamatan.id_kecamatan;
                            option.textContent = kecamatan.nama_kecamatan;
                            kecamatanSelect.appendChild(option);
                        });
                        
                        // Refresh Tom Select instance
                        kecamatanSelect.tomselect.clear();
                        kecamatanSelect.tomselect.clearOptions();
                        kecamatanSelect.tomselect.addOptions(data.map(kec => ({
                            value: kec.id_kecamatan,
                            text: kec.nama_kecamatan
                        })));
                        
                        // Reset dependent select
                        resetSelect('id_desa');
                    });
            }
        
            function fetchDesa(id_kecamatan) {
                fetch(`/get-desa/${id_kecamatan}`)
                    .then(response => response.json())
                    .then(data => {
                        const desaSelect = document.getElementById('id_desa');
                        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
                        
                        data.forEach(desa => {
                            const option = document.createElement('option');
                            option.value = desa.id_desa;
                            option.textContent = desa.nama_desa;
                            desaSelect.appendChild(option);
                        });
                        
                        // Refresh Tom Select instance
                        desaSelect.tomselect.clear();
                        desaSelect.tomselect.clearOptions();
                        desaSelect.tomselect.addOptions(data.map(des => ({
                            value: des.id_desa,
                            text: des.nama_desa
                        })));
                    });
            }
        
            function resetSelect(selectId) {
                const select = document.getElementById(selectId);
                select.innerHTML = `<option value="">Pilih ${selectId.split('_')[1].charAt(0).toUpperCase() + selectId.split('_')[1].slice(1)}</option>`;
                select.tomselect.clear();
                select.tomselect.clearOptions();
            }
        });
        </script>
</body>
</html>