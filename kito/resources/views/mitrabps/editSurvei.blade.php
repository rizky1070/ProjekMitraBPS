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
    swal("Error!", "{{ $errors->first() }}", "error");
    </script>
    @endif
    
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
        <a href="{{ url('/daftarSurvei') }}"  class="px-4 py-2 bg-orange text-black rounded-bl-none rounded-br-md">
            <
        </a>
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">Survei Terpilih</h2>
            <div class="bg-white p-4 rounded-lg shadow">
                <p><strong>Nama Survei :</strong> {{ $survey->nama_survei }}</p>
                <p><strong>Lokasi :</strong> {{ $survey->kecamatan->nama_kecamatan ?? 'Lokasi tidak tersedia' }}</p>
                <p><strong>Jadwal :</strong> {{ $survey->jadwal_kegiatan }}</p>
                <p><strong>Tim :</strong> Pertanian</p>
                <p><strong>KRO :</strong> {{ $survey->kro }} </p>
                <div class="flex items-center">
                <p>
                    <strong>Status :</strong>
                    <span class="font-bold">
                        @if($survey->status_survei == 1)
                            <div class="bg-red-500 text-white rounded-md px-1 py-0.5 ml-2 mr-5">Belum Dikerjakan</div>
                        @elseif($survey->status_survei == 2)
                            <div class="bg-yellow-300 text-white rounded-md px-1 py-0.5 ml-2 mr-5">Sedang Dikerjakan</div>
                        @elseif($survey->status_survei == 3)
                            <div class="bg-green-500 text-white rounded-md px-1 py-0.5 ml-2 mr-5">Sudah Dikerjakan</div>
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
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Belum Dikerjakan</button>
                        </form>
                        <form action="{{ route('survey.updateStatus', $survey->id_survei) }}" method="POST" class="block">
                            @csrf
                            <input type="hidden" name="status_survei" value="2">
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sedang Dikerjakan</button>
                        </form>
                        <form action="{{ route('survey.updateStatus', $survey->id_survei) }}" method="POST" class="block">
                            @csrf
                            <input type="hidden" name="status_survei" value="3">
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sudah Dikerjakan</button>
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

            <h3 class="text-xl font-bold mt-6 mb-4">Daftar Mitra</h3>
            <div class="bg-white p-4 rounded-lg shadow">
                <div x-data="{ isOpen: false }">
                    <!-- Form untuk Filter dan Tombol Tambah -->
                    <div class="flex justify-between items-center mb-4">
                        <button @click="isOpen = true" class="px-4 py-2 bg-orange text-white rounded-md hover:bg-orange-600 transition duration-300">
                            Filter
                        </button>
                        <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Tambah</button>
                    </div>
                
                    <!-- Modal untuk Filter -->
                    <div x-show="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
                            <h2 class="text-xl font-bold mb-6 text-gray-800">Filter</h2>
                
                            <!-- Form Filter di dalam Modal -->
                            <form action="{{ route('editSurvei.filter', ['id_survei' => $survey->id_survei]) }}" method="GET" class="space-y-4">

                                <!-- Dropdown untuk memilih mitra -->
                                <div>
                                    <select id="mitra"  name="mitra" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <option value="">Pilih Mitra</option>
                                        @foreach($mitrasForDropdown as $mitra)
                                            <option value="{{ $mitra->id_mitra }}" @if(request('mitra') == $mitra->id_mitra) selected @endif>{{ $mitra->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                
                                <!-- Dropdown untuk memilih kecamatan -->
                                <div>
                                    <select id="kecamatan"  name="kecamatan" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($kecamatans as $kecamatan)
                                            <option value="{{ $kecamatan->id_kecamatan }}" @if(request('kecamatan') == $kecamatan->id_kecamatan) selected @endif>{{ $kecamatan->nama_kecamatan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                
                                <!-- Tombol Apply Filter -->
                                <button type="submit" class="w-full px-4 py-2 bg-orange text-white rounded-md hover:bg-orange-600 transition duration-300">
                                    Apply Filter
                                </button>
                            </form>
                
                            <!-- Tombol untuk menutup modal -->
                            <button @click="isOpen = false" class="mt-4 w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
                <!-- JavaScript Tom Select -->
                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                <!-- Inisialisasi Tom Select -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {

                        new TomSelect('#kecamatan', {
                            placeholder: 'Pilih Kecamatan',
                            searchField: 'text',
                        });

                        new TomSelect('#mitra', {
                            placeholder: 'Pilih Mitra',
                            searchField: 'text',
                        });
                    });
                </script>
                

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
                        @foreach($mitras as $mitra)
                        <tr class="bg-white hover:bg-gray-100">
                            <td class="border border-gray-300 p-2">{{ $mitra->nama_lengkap }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $mitra->kecamatan->nama_kecamatan ?? 'Lokasi tidak tersedia' }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $mitra->mitra_survei_count }}</td>
                            <td class="border border-gray-300 p-2 text-center">
                                @if ($mitra->isFollowingSurvey)
                                    <form action="{{ route('mitra.toggle', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-orange text-white px-3 py-1 rounded">Hapus</button>
                                    </form>
                                @else
                                    <form action="{{ route('mitra.toggle', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-orange text-white px-3 py-1 rounded">Tambah</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('components.pagination', ['paginator' => $mitras])
        </div>
    </main>
    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-2">Unggah File Excel</h2>
            <p class="mb-2">Tambahkan mitra ke survei dengan file excel.</p>
            <form action="{{ route('upload.excel', ['id_survei' => $survey->id_survei]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                <div class="flex justify-end mt-4">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md mr-2" onclick="closeModal()">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange text-white rounded-md">Unggah</button>
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