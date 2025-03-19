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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Daftar Survei BPS</title>
</head>

<body class="h-full">
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
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6">

    <div class="flex justify-between items-start"> <!-- Membuat tampilan dua kolom dengan jarak -->
        
        <!-- Kiri: Profil Mitra -->
        <div class="w-1/3 text-center mr-auto"> <!-- Menjauhkan dari kanan -->
            <h2 class="text-xl font-bold mb-4">Profil Mitra</h2> <!-- Judul di atas -->
            <img alt="Profile picture" class="w-24 h-24 rounded-full mr-4" 
            src="logo.png" width="100" height="100">            
            <h2 class="text-lg font-semibold mt-2">{{ $surMit->mitra->nama_lengkap ?? '-' }}</h2> <!-- Nama mitra di bawah foto -->
        </div>

        <!-- Kanan: Detail Mitra -->
        <div class="w-2/3 border-l pl-10 ml-auto"> <!-- Memberi jarak lebih besar dari kiri -->
            <div class="grid grid-cols-2 gap-4 text-sm"> <!-- Menambah gap agar lebih rapi -->
                <span class="font-semibold">Survei / Sensus:</span>
                <span>{{ $surMit->survei->nama_survei ?? '-' }}</span>
                <span class="font-semibold">Kecamatan:</span>
                <span>{{ $surMit->survei->kecamatan->nama_kecamatan ?? '-' }}</span>
                <span class="font-semibold">Lokasi:</span>
                <span>{{ $surMit->survei->lokasi_survei ?? '-' }}</span>
                <span class="font-semibold">Posisi:</span>
                <span>{{ $surMit->posisi_mitra ?? '-' }}</span>
                <span class="font-semibold">Jadwal:</span>
                <span>{{ $surMit->survei->jadwal_kegiatan ?? '-' }}</span>
            </div>
        </div>

    </div>


        <div class="p-4 border rounded-lg shadow-md w-1000 mx-auto">
            <form class="w-1000" action="{{ route('simpan.penilaian') }}" method="POST">
                @csrf
                <input type="hidden" name="id_mitra_survei" value="{{ $surMit->id_mitra_survei }}">

                <!-- Rating Bintang -->
                <div class="flex justify-center mb-4">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" class="star text-4xl focus:outline-none" data-value="{{ $i }}">★</button>
                    @endfor
                </div>
                <input type="hidden" name="nilai" id="rating" value="0">

                <!-- Catatan -->
                <label class="block text-lg font-semibold text-center">Catatan:</label>
                <textarea name="catatan"
                    class="w-full mt-2 p-3 border rounded-lg text-gray-600 focus:ring focus:ring-yellow-400"
                    placeholder="Catatan untuk mitra" rows="4"></textarea>

                <!-- Tombol Tambah -->
                <button type="submit"
                    class="w-full bg-orange text-white font-semibold py-2 rounded-lg mt-4 hover:bg-orange-500 transition">
                    Tambah
                </button>
            </form>
        </div>

        <!-- Script untuk Interaksi Rating -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const stars = document.querySelectorAll(".star");
                const ratingInput = document.getElementById("rating");

                stars.forEach((star, index) => {
                    star.addEventListener("click", function () {
                        let value = index + 1;
                        ratingInput.value = value;
                        stars.forEach((s, i) => {
                            s.style.color = i < value ? "yellow" : "gray";
                        });
                    });
                });
            });
        </script>
    </div>


</body>

</html>