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

    
    <a href="{{ url()->previous() }}" class="px-4 py-2 bg-orange text-black rounded-bl-none rounded-br-md">
        <
    </a>

    <!-- component -->
    <main class="max-w-4xl mx-auto bg-gray-200">

        <div class="flex justify-between items-center max-w-4xl mx-auto mt-4"> <!-- Membuat tampilan dua kolom dengan jarak -->
            
            <!-- Kiri: Profil Mitra -->
            <div class="max-w-4xl mx-auto mt-4 w-full">
                <h1 class="text-2xl font-bold">Profil Mitra</h1>
            <div class="flex items-center bg-white my-4 px-10 py-5 rounded-lg shadow w-full">
                <div class="flex flex-col justify-center items-center text-center">
                    <img alt="Profile picture" class="w-24 h-24 rounded-full border-2 border-gray-500 mr-4" src="{{ asset('person.png') }}" width="100" height="100">
                    <h2 class="text-xl">{{ $surMit->mitra->nama_lengkap }}</h2>
                </div>
                <div class="pl-5 w-full"> 
                    <p><strong>Survei / Sensus : </strong>{{ $surMit->survei->nama_survei ?? '-' }}</p>
                    <p><strong>Kecamatan : </strong>{{ $surMit->survei->kecamatan->nama_kecamatan ?? '-' }}</p>
                    <p><strong>Lokasi : </strong>{{ $surMit->survei->lokasi_survei ?? '-' }}</p>
                    <p><strong>Posisi : </strong>{{ $surMit->posisi_mitra ?? '-' }}</p>
                    <p><strong>Jadwal : </strong>{{ $surMit->survei->jadwal_kegiatan ?? '-' }}</p>       
                </div>
            </div>
            </div>

        </div>

        <div>  
            <h1 class="text-2xl font-bold my-4">Penilaian</h1>
            <div class="p-4 bg-white border rounded-lg shadow-md mx-auto">
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
                    <div class="flex justify-center">
                        <button type="submit"
                            class="w-full max-w-[150px] bg-orange text-black font-semibold py-2 rounded-lg mt-4">
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
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
    </main>


</body>

</html>