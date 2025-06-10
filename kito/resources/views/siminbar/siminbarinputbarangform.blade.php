<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Menambahkan file CSS dan JS dengan Laravel Vite -->
    @include('viteall')
        <!-- Library Leaflet untuk peta -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Favicon -->
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Tambah Barang</title>
</head>

<body>
    <!-- Tombol kembali ke halaman sebelumnya -->
    <a href="/agenkitapresensi" class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- Ikon panah kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <!-- Container utama -->
    <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <!-- Judul Form -->
            <h2 class="text-2xl font-semibold mb-4 text-center">Form Tambah Stok</h2>
            <h2 class="text-2xl font-semibold mb-4 text-left">{{ $barang['namabarang'] }}</h2>

            <!-- Formulir input barang -->
            <form action="{{ route('siminbarinputbarang.store') }}" method="POST">
                <!-- Token CSRF untuk keamanan -->
                @csrf

                <!-- Input Tanggal -->
                <div class="flex items-center space-x-4">
                    <div>
                        <label for="inputdate" class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                        <input id="inputdate" name="inputdate" type="date" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>
                </div>

               <!-- jam -->
            <div class="mt-4 flex space-x-4">
                <div>
                    <label for="inputtime" class="block mb-2 text-sm font-medium text-gray-900">Waktu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="time" id="inputtime" name="inputtime" class="bg-white border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>
                </div>
            </div>
                <!-- Input Jumlah Tambah -->
                <div class="mt-4">
                    <label for="jumlahtambah" class="block text-sm font-medium text-gray-700">Jumlah Tambah Barang</label>
                    <input type="number" id="jumlahtambah" name="jumlahtambah" placeholder="Jumlah Tambah" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <!-- Input Stok Barang -->
                <div class="mt-4">
                    <label for="stoktersedia" class="block text-sm font-medium text-gray-700">Stok Sebelum Ditambah</label>
                    <input type="number" id="stoktersedia" name="stoktersedia" value="{{ $barang['stoktersedia'] }}" class="bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg block w-full p-2.5" readonly>
                </div>

                <!-- Input ID Barang -->
                <input type="hidden" id="barang_id" name="id" value="{{ $barang['id'] }}">

                <!-- Tombol Submit -->
                <div class="mt-6">
                    <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk mendapatkan tanggal saat ini dalam format yyyy-mm-dd
        function getCurrentDate() {
            return new Date().toISOString().split('T')[0];
        }

        // Fungsi untuk mendapatkan waktu saat ini dalam format HH:MM
        function getCurrentTime() {
            const now = new Date();
            return now.toLocaleTimeString('en-US', { hour12: false }).substring(0, 5);
        }

        // Menetapkan nilai default untuk input tanggal dan waktu saat halaman dimuat
        window.onload = function () {
            document.getElementById('inputdate').value = getCurrentDate();
            document.getElementById('inputtime').value = getCurrentTime();
        };
    </script>
</body>

</html>
