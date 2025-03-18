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
                <p><strong>KRO :</strong>{{ $survey->kro }}</p>
            </div>

            <h3 class="text-xl font-bold mt-6 mb-4">Daftar Mitra</h3>
            <div class="bg-white p-4 rounded-lg shadow">
                <form action="{{ route('pilihSurvei.filter', ['id_survei' => $survey->id_survei]) }}" method="GET" class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-md">
                        <select name="kecamatan" class="px-4 py-2 border border-gray-300 rounded-md">
                            <option value="">Pilih Kecamatan</option>
                            @foreach($kecamatans as $kecamatan)
                                <option value="{{ $kecamatan->id_kecamatan }}" @if(request('kecamatan') == $kecamatan->id_kecamatan) selected @endif>{{ $kecamatan->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-orange rounded-md">Filter</button>
                    </div>
                    <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Tambah</button>
                </form>

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
        </div>
    </main>

    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Unggah File Excel</h2>
            <form action="{{ route('upload.excel') }}" method="POST" enctype="multipart/form-data">
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