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

    <title>Input Mitra BPS</title>
</head>
<body class="h-full">
    @if (session('success'))
    <script>
    swal("Success!", "{{ session('success') }}", "success");
    </script>
    @endif

    @if ($errors->any())
    <script>
    swal("Error!", "{{ implode(', ', $errors->all()) }}", "error");
    </script>
    @endif
    
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
                        <p><strong>Kecamatan :</strong> {{ $survey->kecamatan->nama_kecamatan ?? 'Lokasi tidak tersedia' }}</p>
                        <p><strong>Jadwal :</strong> {{ \Carbon\Carbon::parse($survey->jadwal_kegiatan )->translatedFormat('j F Y') }}</p>
                    </div>
                    <div class="w-full md:w-1/2">
                        <p><strong>Tim :</strong> {{ $survey->tim }}</p>
                        <p><strong>KRO :</strong> {{ $survey->kro }} </p><div class="flex items-center">
                    </div>
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

                        <!-- Dropdown -->
                <div class="relative inline-block text-left ml-4">
                    <button type="button" class="bg-orange text-black px-2 py-1 rounded" onclick="toggleDropdown()">Ubah Status</button>
                    <div id="dropdown" class="hidden absolute mt-2 bg-white border rounded shadow-lg z-10">
                        <form action="{{ route('survey.updateStatus', $survey->id_survei) }}" method="POST" class="block">
                            @csrf
                            <input type="hidden" name="status_survei" value="1">
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Belum Dikerjakan</button>
                        </form>
                        <form action="{{ route('survey.updateStatus', $survey->id_survei) }}" method="POST" class="block">
                            @csrf
                            <input type="hidden" name="status_survei" value="2">
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Sedang Dikerjakan</button>
                        </form>
                        <form action="{{ route('survey.updateStatus', $survey->id_survei) }}" method="POST" class="block">
                            @csrf
                            <input type="hidden" name="status_survei" value="3">
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Sudah Dikerjakan</button>
                        </form>
                    </div>
                </div>
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
                <button type="button" class="mt-4 px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Tambah</button>
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
                <div class="overflow-x-auto mx-6 p-2 bg-white rounded-lg shadow-md">
                    <table class="w-full border-collapse border border-gray-350">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 p-2">Nama Mitra</th>
                                <th class="border border-gray-300 p-2">Domisili</th>
                                <th class="border border-gray-300 p-2">Survei yang Diikuti</th>
                                <th class="border border-gray-300 p-2">Tahun Mitra diterima</th>
                                <th class="border border-gray-300 p-2">Vol</th>
                                <th class="border border-gray-300 p-2">Rate Honor</th>
                                <th class="border border-gray-300 p-2">Posisi</th>
                                <th class="border border-gray-300 p-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mitras as $mitra)
                            <tr class="bg-white hover:bg-gray-200">
                                <td class="border border-gray-350 p-2 transition-all duration-300 ease-in-out transform hover:bg-orange hover:text-white hover:shadow-lg hover:scale-105" style="max-width: 120px;">
                                    <a href="/profilMitra/{{ $mitra->id_mitra }}">{{ $mitra->nama_lengkap }}</a>
                                </td>
                                <td class="border border-gray-350 p-2 text-center" style="max-width: 120px;">{{ $mitra->kecamatan->nama_kecamatan ?? 'Lokasi tidak tersedia' }}</td>
                                <td class="border border-gray-350 p-2 text-center" style="max-width: 120px;">{{ $mitra->mitra_survei_count }}</td>
                                <td class="border border-gray-350 p-2 text-center" style="max-width: 120px;">{{ \Carbon\Carbon::parse($mitra->tahun )->translatedFormat('j F Y') }}</td>

                                
                                @if ($mitra->vol && $mitra->honor && $mitra->posisi_mitra)
                                <!-- Vol -->
                                <form action="{{ route('mitra.update', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                    @csrf
                                    <td class="border border-gray-350 p-0 text-center" style="max-width: 120px;">
                                        <input type="text" name="vol" value="{{ $mitra->vol }}" class="w-full focus:outline-none text-center border-none hover:bg-gray-200" placeholder="{{ $mitra->vol }}" style="width: 100%;">
                                    </td>
                                    <td class="border border-gray-350 p-0 text-center" style="max-width: 120px;">
                                        <input type="text" name="honor" value="{{ $mitra->honor }}" class="w-full focus:outline-none text-center border-none hover:bg-gray-200" placeholder="Rp{{ number_format($mitra->honor, 0, ',', '.') }}" style="width: 100%;">
                                    </td>
                                    <td class="border border-gray-350 p-0 text-center" style="max-width: 120px;">
                                        <input type="text" name="posisi_mitra" value="{{ $mitra->posisi_mitra }}" class="w-full focus:outline-none text-center border-none hover:bg-gray-200" placeholder="{{ $mitra->posisi_mitra }}" style="width: 100%;">
                                    </td>
                                    <td class="flex justify-center py-2 text-center">
                                        <button type="submit" class="bg-orange text-black px-3 mx-1 rounded">Ubah</button>
                                </form>
                                <form action="{{ route('mitra.delete', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-orange text-black px-3 mx-1 rounded">Hapus</button>
                                    </td>
                                </form>
                                @else
                                <td class="border border-gray-350 p-0 text-center" style="max-width: 120px;">
                                    <form action="{{ route('mitra.toggle', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                        <input type="text" name="vol" value="{{ $mitra->vol }}" class="w-full focus:outline-none text-center border-none hover:bg-gray-200" placeholder="Masukkan Vol" style="width: 100%;">
                                </td>
                                <td class="border border-gray-350 p-0 text-center" style="max-width: 120px;">
                                        <input type="text" name="honor" value="{{ $mitra->honor }}" class="w-full focus:outline-none text-center border-none hover:bg-gray-200" placeholder="Masukkan Honor" style="width: 100%;">
                                </td>
                                <td class="border border-gray-350 p-0 text-center" style="max-width: 120px;">
                                        <input type="text" name="posisi_mitra" value="{{ $mitra->posisi_mitra }}" class="w-full focus:outline-none text-center border-none hover:bg-gray-200" placeholder="Masukkan Posisi Mitra" style="width: 100%;">
                                </td>
                                <td class="border border-gray-350 p-2 text-center" style="max-width: 120px;">
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
            @include('components.pagination', ['paginator' => $mitras])
        </div>
    </main>
    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden" style="z-index: 50;">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-2">Import Mitra ke Survei</h2>
            <p class="mb-2 text-red-700 text-sm">Pastikan format file excel yang diimport sesuai.</p>
            <form action="{{ route('upload.excel', ['id_survei' => $survey->id_survei]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                    <a href="{{ asset('addMitra2Survey.xlsx') }}" class="py-2 text-blue-500 hover:text-blue-600 text-xs">
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