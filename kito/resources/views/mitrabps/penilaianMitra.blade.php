<?php
$title = 'Penilaian Mitra';
?>
@include('mitrabps.headerTemp')
</head>

<body class="h-full bg-gray-200 font-sans">
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
       class="inline-flex items-center gap-2 px-4 py-2 bg-oren hover:bg-orange-500 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
    </a>

    <main class="max-w-4xl mx-auto p-2 sm:p-4">

        <div class="flex flex-col md:flex-row items-center bg-white my-4 px-4 py-4 md:px-6 md:py-5 rounded-lg shadow w-full">
            <div class="flex flex-col justify-center items-center text-center mb-4 md:mb-0 md:mr-6">
                <img alt="Profile picture" class="w-32 rounded-full border-4 object-cover {{ $surMit->mitra->jenis_kelamin == 2 ? 'border-pink-500' : 'border-blue-500' }}"
                     src="{{ $profileImage }}"
                        onerror="this.onerror=null;this.src='{{ asset('person.png') }}'"
                     width="100" height="100">
                <h2 class="text-lg md:text-xl font-bold mt-2">{{ $surMit->mitra->nama_lengkap }}</h2>
                <h5 class="my-1 md:my-2 text-sm text-gray-600">{{ $surMit->mitra->sobat_id }}</h5>
            </div>

            <div class="w-full text-sm md:text-base">
                <div class="flex justify-between w-full border-b py-2">
                    <strong class="mr-2">Survei / Sensus:</strong>
                    <span class="text-right">{{ $surMit->survei->nama_survei ?? '-' }}</span>
                </div>
                <div class="flex justify-between w-full border-b py-2">
                    <strong class="mr-2">Posisi:</strong>
                    <span class="text-right">{{ $surMit->posisiMitra->nama_posisi ?? '-' }}</span>
                </div>
                <div class="flex justify-between w-full py-2">
                    <strong class="mr-2">Jadwal:</strong>
                    <span class="text-right">{{ \Carbon\Carbon::parse($surMit->survei->jadwal_kegiatan)->translatedFormat('j F Y') }} -
                                        {{ \Carbon\Carbon::parse($surMit->survei->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}</span>
                </div>
            </div>
        </div>

        <div class="w-full bg-white my-4 p-4 md:p-6 rounded-lg shadow">
            <h1 class="text-xl md:text-2xl font-bold mb-4 text-center">Penilaian Mitra</h1>
            <form action="{{ route('simpan.penilaian') }}" method="POST">
                @csrf
                <input type="hidden" name="id_mitra_survei" value="{{ $surMit->id_mitra_survei }}">

                <div class="flex justify-center mb-4">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" class="star text-4xl sm:text-5xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors duration-200 px-1" data-value="{{ $i }}">★</button>
                    @endfor
                </div>
                <input type="hidden" name="nilai" id="rating" value="0" required>

                <div class="mt-4">
                    <label for="catatan" class="block text-base md:text-lg font-semibold text-center mb-2">Catatan:</label>
                    <textarea id="catatan" name="catatan"
                        class="w-full p-3 border rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm md:text-base"
                        placeholder="Berikan catatan mengenai kinerja mitra..." rows="4"></textarea>
                </div>

                <div class="flex justify-center mt-6">
                    <button type="submit"
                        class="w-full max-w-xs bg-oren py-3 rounded-lg text-white font-semibold hover:bg-orange-500 hover:shadow-lg transition-all duration-300">
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const stars = document.querySelectorAll(".star");
            const ratingInput = document.getElementById("rating");

            stars.forEach((star) => {
                star.addEventListener("click", function () {
                    let value = this.dataset.value;
                    ratingInput.value = value;
                    
                    stars.forEach((s, i) => {
                        s.classList.toggle("text-yellow-400", i < value);
                        s.classList.toggle("text-gray-300", i >= value);
                    });
                });
            });
        });
    </script>

</body>
</html>