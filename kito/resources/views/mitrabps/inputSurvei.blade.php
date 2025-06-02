<?php
$title = 'Input Survei';
?>
@include('mitrabps.headerTemp')
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
                    <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Import Survei</button>
                </div>
                @if(session('import_errors'))
                <div class="mt-2 mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700">
                    <h4 class="font-bold">Survei yang gagal diimport:</h4>
                    <ul class="cuScrollError list-disc pl-5">
                        @foreach(session('import_errors') as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
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
                                <label for="kro" class="block text-sm font-medium text-gray-700 mb-1">KRO</label>
                                <input type="text" name="kro" id="kro" value="{{ old('kro') }}" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm" placeholder="KRO">
                            </div>
                            
                            <div class="mb-5">
                                <label for="tim" class="block text-sm font-medium text-gray-700 mb-1">Tim</label>
                                <input type="text" name="tim" id="tim" value="{{ old('tim') }}" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm" placeholder="Tim">
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan -->
                        <div class="w-full md:w-1/2 px-3">
                            
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
            
            <!-- Tambahkan bagian ini untuk menampilkan error import -->
            
            <form action="{{ route('upload.excelSurvei') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                <p class="py-2 text-s">Belum punya file excel?  
                    <a href="{{ asset('addSurvey.xlsx') }}" class="text-blue-500 hover:text-blue-600 font-bold">
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