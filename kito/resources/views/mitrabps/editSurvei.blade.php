<?php
$title = 'Kelola Survei';
?>
@include('mitrabps.headerTemp')
<!-- Add jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<style>
    .honor-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .honor-modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
    }
</style>
</head>
<body class="h-full bg-gray-200 mb-4">
    @if (session('success'))
    <script>
    swal("Success!", "{{ session('success') }}", "success");
    </script>
    @endif

    @if (session('error'))
    <script>
    swal("Error!", "{!! session('error') !!}", "error");
    </script>
    @endif
    
    @if ($errors->any())
    <script>
    swal("Error!", "{!! implode(', ', $errors->all()) !!}", "error");
    </script>
    @endif
    
    <!-- Add this section for honor limit confirmation -->
    @if (session('confirm'))
    <div class="honor-modal" id="honorConfirmModal">
        <div class="honor-modal-content">
            <h3 class="text-lg font-bold mb-4">Konfirmasi Tambah Mitra</h3>
            <p class="mb-4">{!! session('confirm')['message'] !!}</p>
            
            <div class="flex justify-end space-x-3">
                <form method="POST" action="{{ route('mitra.toggle', [
                    'id_survei' => $survey->id_survei, 
                    'id_mitra' => session('id_mitra')
                ]) }}">
                    @csrf
                    @foreach(session('confirm')['data'] as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="force_add" value="1">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-black rounded">
                        Ya, Tambahkan
                    </button>
                </form>
                <button onclick="closeHonorModal()" class="px-4 py-2 bg-gray-300 rounded">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        // Tampilkan modal saat ada konfirmasi
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('confirm'))
                document.getElementById('honorConfirmModal').style.display = 'flex';
            @endif
        });

        function closeHonorModal() {
            document.getElementById('honorConfirmModal').style.display = 'none';
        }
    </script>
    @endif

    <!-- Form Tambah/Edit Mitra -->
    @foreach($mitras as $mitra)
        @if ($mitra->vol && $mitra->honor && $mitra->posisi_mitra)
            <!-- Form Edit -->
            <form action="{{ route('mitra.update', [
                'id_survei' => $survey->id_survei,
                'id_mitra' => $mitra->id_mitra
            ]) }}" method="POST">
                @csrf
                <!-- Input fields -->
            </form>
        @else
            <!-- Form Tambah -->
            <form action="{{ route('mitra.toggle', [
                'id_survei' => $survey->id_survei,
                'id_mitra' => $mitra->id_mitra
            ]) }}" method="POST">
                @csrf
                <!-- Input fields -->
            </form>
        @endif
    @endforeach

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
    <a href="{{ url('/daftarSurvei') }}" 
    class="inline-flex items-center gap-2 px-4 py-2 bg-orange hover:bg-orange-600 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

        <div class="p-4">
            <h2 class="text-2xl font-bold mb-4">Detail Survei</h2>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xl font-medium"><strong>Nama Survei :</strong> {{ $survey->nama_survei }}</p>
                <div class="flex flex-col md:flex-row items-start md:items-center w-full">
                    <div class="w-full md:w-1/2">
                        <p><strong>Pelaksanaan :</strong> {{ \Carbon\Carbon::parse($survey->jadwal_kegiatan )->translatedFormat('j F Y') }} - {{ \Carbon\Carbon::parse($survey->jadwal_berakhir_kegiatan )->translatedFormat('j F Y') }}</p>
                        <p><strong>Tim :</strong> {{ $survey->tim }}</p>
                        <p><strong>KRO :</strong> {{ $survey->kro }} </p><div class="flex items-center">
                    </div>
                    <!-- <div class="w-full md:w-1/2">
                    </div> -->
                </div>
            </div>
            
            <div class="flex items-center">
                <p><strong>Status :</strong>
                    <span class="font-bold">
                        @if($survey->status_survei == 1)
                            <div class="bg-red-500 text-white  px-2 py-1 rounded ml-2 mr-5">Belum Dikerjakan</div>
                        @elseif($survey->status_survei == 2)
                            <div class="bg-yellow-300 text-white  px-2 py-1 rounded ml-2 mr-5">Sedang Dikerjakan</div>
                        @elseif($survey->status_survei == 3)
                            <div class="bg-green-500 text-white  px-2 py-1 rounded ml-2 mr-5">Sudah Dikerjakan</div>
                        @else
                            <span class="bg-gray-500 text-white rounded-md px-2 py-1 ml-2">Status Tidak Diketahui</span>
                        @endif
                    </span>
                </p>
            </div>
            
            <script>
                function toggleDropdown() {
                    var dropdown = document.getElementById("dropdown");
                    dropdown.classList.toggle("hidden");
                }
            </script>

            </div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold mt-4">Daftar Mitra</h3>
                <div class="flex gap-2">
                    <form action="{{ route('survey.delete', ['id_survei' => $survey->id_survei]) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus survei {{ $survey->nama_survei }}? SEMUA MITRA YANG TERKAIT AKAN DIPUTUSKAN RELASINYA.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mt-4 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                            Hapus Survei
                        </button>
                    </form>
                    <button type="button" class="mt-4 px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Tambah</button>
                </div>
            </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <!-- Year Row -->
                    <form id="filterForm" action="{{ route('editSurvei.filter', ['id_survei' => $survey->id_survei]) }}" method="GET" class="space-y-4">
                        <!-- Survey Name Row -->
                        <div class="flex items-center relative">
                            <label for="nama_lengkap" class="w-32 text-lg font-semibold text-gray-800 mb-4">Cari Mitra</label>
                            <select name="nama_lengkap" id="nama_mitra" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($namaMitraOptions) ? 'disabled' : '' }}>
                                <option value="">Semua Mitra</option>
                                @foreach($namaMitraOptions as $nama => $label)
                                    <option value="{{ $nama }}" @if(request('nama_lengkap') == $nama) selected @endif>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Filter Mitra</h2>
                        </div>
                        <div class="flex">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                                <div class="flex items-center">
                                    <label for="tahun" class="w-32 text-sm font-medium text-gray-700">Tahun</label>
                                    <select name="tahun" id="tahun" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                        <option value="">Semua Tahun</option>
                                            @foreach($tahunOptions as $year => $yearLabel)
                                            <option value="{{ $year }}" @if(request('tahun') == $year) selected @endif>{{ $yearLabel }}</option>
                                            @endforeach
                                    </select>
                                </div>
                                <!-- Month Row -->
                                <div class="flex items-center">
                                    <label for="bulan" class="w-32 text-sm font-medium text-gray-700">Bulan</label>
                                    <select name="bulan" id="bulan" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                        <option value="">Semua Bulan</option>
                                        @foreach($bulanOptions as $month => $monthName)
                                            <option value="{{ $month }}" @if(request('bulan') == $month) selected @endif>{{ $monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- District Row -->
                                <div class="flex items-center">
                                    <label for="kecamatan" class="w-32 text-sm font-medium text-gray-700">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($kecamatanOptions) ? 'disabled' : '' }}>
                                        <option value="">Semua Kecamatan</option>
                                        @foreach($kecamatanOptions as $kecam)
                                            <option value="{{ $kecam->id_kecamatan }}" @if(request('kecamatan') == $kecam->id_kecamatan) selected @endif>
                                                [{{ $kecam->kode_kecamatan }}] {{ $kecam->nama_kecamatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
                <!-- JavaScript Tom Select -->
            <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                <!-- Inisialisasi Tom Select -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    new TomSelect('#nama_mitra', {
                    placeholder: 'Pilih Mitra',
                        searchField: 'text',
                    });
                    
                    new TomSelect('#tahun', {
                        placeholder: 'Pilih Tahun',
                        searchField: 'text',
                    });

                    new TomSelect('#bulan', {
                        placeholder: 'Pilih Bulan',
                        searchField: 'text',
                    });

                    new TomSelect('#kecamatan', {
                        placeholder: 'Pilih Kecamatan',
                        searchField: 'text',
                    });

                        // Auto submit saat filter berubah
                    const filterForm = document.getElementById('filterForm');
                    const tahunSelect = document.getElementById('tahun');
                    const bulanSelect = document.getElementById('bulan');
                    const kecamatanSelect = document.getElementById('kecamatan');
                    const mitraSelect = document.getElementById('nama_mitra');

                        // Ganti fungsi submitForm dengan ini
                    let timeout;
                    function submitForm() {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            filterForm.submit();
                        }, 500); // Delay 500ms sebelum submit
                    }

                    // Tambahkan event listener untuk setiap select
                    tahunSelect.addEventListener('change', submitForm);
                    bulanSelect.addEventListener('change', submitForm);
                    kecamatanSelect.addEventListener('change', submitForm);
                    mitraSelect.addEventListener('change', submitForm);
                });

                </script>
                <div class="border rounded-lg shadow-sm bg-white bg-white p-2 mx-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-500">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mitra</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Domisili</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Survei yang Diikuti</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Masa Kontrak</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vol</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rate Honor</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-500">
                                @foreach($mitras as $mitra)
                                <tr class="hover:bg-gray-50" style=" border-top-width: 2px; border-color: #D1D5DB;">
                                    @php
                                        if ($mitra->status_pekerjaan == 1) {
                                            $bgStatus = 'bg-red-500';
                                        } else {
                                            $bgStatus = 'bg-green-500';
                                        }
                                    @endphp
                                    <td class="whitespace-normal text-center break-words" style="max-width: 120px;">
                                        <div class="flex justify-center items-center">
                                            <a href="/profilMitra/{{ $mitra->id_mitra }}">{{ $mitra->nama_lengkap }}</a>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap text-center" style="max-width: 120px;">{{ $mitra->kecamatan->nama_kecamatan ?? 'Lokasi tidak tersedia' }}</td>
                                    <td class="whitespace-nowrap text-center" style="max-width: 100px;">{{ $mitra->mitra_survei_count }}</td>
                                    <td class="text-center whitespace-normal break-words" style="max-width: 120px;">
                                        {{ \Carbon\Carbon::parse($mitra->tahun)->translatedFormat('j F Y') }} - 
                                        {{ \Carbon\Carbon::parse($mitra->tahun_selesai)->translatedFormat('j F Y') }}
                                    </td>
                                    <?php
                                    $totalHonor = $mitra->honor * $mitra->vol;
                                    ?>
                                    @if ($mitra->posisi_mitra)
                                    <!-- Vol -->
                                    <form action="{{ route('mitra.update', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                        @csrf
                                        <td class=" whitespace-nowrap text-center" style="max-width: 120px;">
                                            <input type="text" name="vol" value="{{ $mitra->vol }}" class="w-full focus:outline-none text-center border-none" placeholder="{{ $mitra->vol }}" style="width: 100%;">
                                        </td>
                                        <td class=" whitespace-nowrap text-center" style="max-width: 120px;">
                                            <input type="text" name="honor" value="{{ $mitra->honor }}" class="w-full focus:outline-none text-center border-none" placeholder="Rp{{ number_format($mitra->honor, 0, ',', '.') }}" style="width: 100%;">
                                        </td>
                                        <td class=" whitespace-nowrap text-center" style="max-width: 120px;">
                                            <input type="text" name="posisi_mitra" value="{{ $mitra->posisi_mitra }}" class="w-full focus:outline-none text-center border-none" placeholder="{{ $mitra->posisi_mitra }}" style="width: 100%;">
                                        </td>
                                        <td>
                                            <div class="flex justify-center items-center py-2 text-center">
                                                <button type="submit" class="bg-orange text-black px-3 mx-1 rounded">Ubah</button>
                                        </form>
                                        <form action="{{ route('mitra.delete', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-orange text-black px-3 mx-1 rounded">Hapus</button>
                                            </div>
                                        </td>
                                    </form>

                                    @else
                                    <td class=" whitespace-nowrap text-center" style="max-width: 120px;">
                                        <form action="{{ route('mitra.toggle', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                            <input type="text" name="vol" value="{{ $mitra->vol }}" class="w-full focus:outline-none text-center border-none" placeholder="Masukkan Vol" style="width: 100%;">
                                    </td>
                                    <td class=" whitespace-nowrap text-center" style="max-width: 120px;">
                                            <input type="text" name="honor" value="{{ $mitra->honor }}" class="w-full focus:outline-none text-center border-none" placeholder="Masukkan Honor" style="width: 100%;">
                                    </td>
                                    <td class=" whitespace-nowrap text-center" style="max-width: 120px;">
                                            <input type="text" name="posisi_mitra" value="{{ $mitra->posisi_mitra }}" class="w-full focus:outline-none text-center border-none" placeholder="Masukkan Posisi Mitra" style="width: 100%;">
                                    </td>
                                    <td class=" whitespace-nowrap p-2 text-center" style="max-width: 120px;">
                                            @csrf
                                            <button type="submit" class="bg-orange text-black px-3 rounded">Tambah</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('components.pagination', ['paginator' => $mitras])
        </div>
    </main>
    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden" style="z-index: 50;">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-2">Import Mitra ke Survei</h2>
            <p class="mb-2 text-red-700 text-m font-bold">Pastikan format file excel yang diimport sesuai!</p>
            <form action="{{ route('upload.excel', ['id_survei' => $survey->id_survei]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                    <p class="py-2 text-s">Belum punya file excel?  
                        <a href="{{ asset('addMitra2Survey.xlsx') }} " class=" text-blue-500 hover:text-blue-600 font-bold">
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
</body>
</html>