<?php
$title = 'Penilaian Mitra';
?>
@include('mitrabps.headerTemp')
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

    
    <a href="/profilMitra/{{ $surMit->Mitra->id_mitra }}" 
    class="inline-flex items-center gap-2 px-4 py-2 bg-orange hover:bg-orange-600 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
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