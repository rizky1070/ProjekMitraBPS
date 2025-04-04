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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Profil Mitra</title>
</head>
<body class="h-full bg-gray-200">
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
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
        <a href="{{ url('/daftarMitra') }}" class="px-4 py-2 bg-orange text-black rounded-bl-none rounded-br-md">
            <
        </a>
        <div class="max-w-4xl mx-auto mt-4">
            <h1 class="text-2xl font-bold">Profil Mitra</h1>
            <div class="flex items-center bg-white my-4 px-10 py-5 rounded-lg shadow">
                <div class="flex flex-col justify-center items-center text-center">
                    <img alt="Profile picture" class="w-24 h-24 rounded-full border-2 border-gray-500 mr-4" src="{{ asset('person.png') }}" width="100" height="100">
                    <h2 class="text-xl font-bold">{{ $mits->nama_lengkap }}</h2>
                </div>
                <div class="pl-5"> 
                    <p><strong>Kecamatan :</strong> {{ $mits->kecamatan->nama_kecamatan }}</p>
                    <p><strong>Alamat Detail :</strong> {{ $mits->alamat_mitra }}</p>
                    <p><strong>Nomor Handphone :</strong> {{ $mits->no_hp_mitra }}</p>
                    <p><strong>Email :</strong> {{ $mits->email_mitra }}</p>       
                </div>
            </div>
        </div>

        <!-- Tabel Survei -->
        <div class="max-w-4xl mx-auto">
            <h2 class="text-xl font-bold mb-4">Survei yang diikuti mitra</h2>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <!-- Header dengan tombol Tambah Survei -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Filter Survei</h2>
                </div>
                <!-- Form Filter -->
                <form method="GET" action="{{ route('profilMitra.filter', ['id_mitra' => $mits->id_mitra]) }}" class="flex flex-wrap gap-4 items-center mb-4" id="filterForm">
                    <!-- Year Row -->
                    <div class="flex items-center">
                        <label for="tahun" class="w-32 text-sm font-medium text-gray-700">Tahun</label>
                        <select name="tahun" id="tahun" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunOptions as $year => $yearLabel)
                                <option value="{{ $year }}" @if(request('tahun') == $year) selected @endif>{{ $yearLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Row -->
                    <div class="flex items-center">
                        <label for="bulan" class="w-32 text-sm font-medium text-gray-700">Bulan</label>
                        <select name="bulan" id="bulan" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($bulanOptions) ? 'disabled' : '' }}>
                            <option value="">Semua Bulan</option>
                            @foreach($bulanOptions as $month => $monthName)
                                <option value="{{ $month }}" @if(request('bulan') == $month) selected @endif>{{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>

                        <!-- Survey Name Row -->
                    <div class="flex items-center">
                        <label for="nama_survei" class="w-32 text-sm font-medium text-gray-700">Nama Survei</label>
                        <select name="nama_survei" id="nama_survei" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($namaSurveiOptions) ? 'disabled' : '' }}>
                            <option value="">Semua Survei</option>
                            @foreach($namaSurveiOptions as $nama => $label)
                                <option value="{{ $nama }}" @if(request('nama_survei') == $nama) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Survei yang sudah dikerjakan</h2>
                    <table class="w-full mb-10 border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 p-2">Nama Survei</th>
                                <th class="border border-gray-300 p-2">Jadwal Survei</th>
                                <th class="border border-gray-300 p-2">Catatan</th>
                                <th class="border border-gray-300 p-2">Nilai</th>
                                <th class="border border-gray-300 p-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($survei as $sur)
                            @if($sur->survei->status_survei == 3)
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="p-2">{{ $sur->survei->nama_survei }}</td>
                                <td class="p-2 text-center">{{ \Carbon\Carbon::parse( $sur->survei->jadwal_kegiatan )->translatedFormat('j F Y') }}</td>
                                @if($sur->catatan == null & $sur->nilai == null)
                                <td class="p-2 text-center text-red-700 font-bold">Tidak ada catatan</td>
                                <td class="p-2 text-center text-red-700 font-bold">Belum dinilai</td>
                                @elseif($sur->catatan != null & $sur->nilai != null)
                                <td class="p-2 text-center">{{ $sur->catatan }}</td>
                                <?php $nilaiOutput = str_repeat('⭐', $sur->nilai); ?>
                                <td class="p-2 text-center">{{ $nilaiOutput }}</td>
                                @endif
                                <td class="p-2 text-center">
                                    <a href="/penilaianMitra/{{ $sur->survei->id_survei }}" class="px-4 py-1 bg-orange text-black rounded-md">Edit</a>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                <h2 class="text-xl font-bold mb-4">Survei yang belum/sedang dikerjakan</h2>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 p-2">Nama Survei</th>
                                <th class="border border-gray-300 p-2">Jadwal Survei</th>
                                <th class="border border-gray-300 p-2">Lihat Survei</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($survei as $sur)
                            @if($sur->survei->status_survei != 3)
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="p-2">{{ $sur->survei->nama_survei }}</td>
                                <td class="p-2 text-center">{{ \Carbon\Carbon::parse( $sur->survei->jadwal_kegiatan )->translatedFormat('j F Y') }}</td>
                                <td class="p-2 text-center">
                                    <a href="/editSurvei/{{ $sur->survei->id_survei }}" class="px-4 py-1 bg-orange text-black rounded-md">Lihat</a>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
    
                </div>
            </div>
        </div>

        <!-- JavaScript Tom Select -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <!-- Inisialisasi Tom Select -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect('#nama_survei', {
                    placeholder: 'Pilih Survei',
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

                // Auto submit saat filter berubah
                const filterForm = document.getElementById('filterForm');
                const tahunSelect = document.getElementById('tahun');
                const bulanSelect = document.getElementById('bulan');
                const surveiSelect = document.getElementById('nama_survei');

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
                surveiSelect.addEventListener('change', submitForm);
            });
        </script>
            
        </div>
    </main>


</body>
<!-- ⭐⭐⭐⭐⭐ -->
</html>
