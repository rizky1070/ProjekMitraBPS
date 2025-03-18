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
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">

                <!-- Main Content -->
                <div class="flex-1 p-6">
                    <!-- Header -->
                    <div class="bg-orange-500 text-white text-xl p-3 font-bold">06. Penilaian Mitra</div>

                    <!-- Profil Mitra -->
                    <div class="bg-white p-6 rounded-lg shadow-md mt-4">
                        <div class="flex">
                            <!-- Foto Profil -->
                            <div class="w-1/4 flex flex-col items-center">
                                <img src="{{ asset('images/profile-placeholder.png') }}"
                                    class="w-20 h-20 rounded-full border" alt="Foto Mitra">
                                <p class="font-bold mt-2">El Fulano</p>
                            </div>

                            <!-- Informasi Mitra -->
                            <div class="w-3/4">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>Survei / Sensus:</div>
                                    <div class="font-bold">Kec. Bangsal</div>
                                    <div>Lokasi:</div>
                                    <div class="font-bold">Desa Dusun</div>
                                    <div>Posisi:</div>
                                    <div class="font-bold">081********</div>
                                    <div>Jadwal:</div>
                                    <div class="font-bold">yadayadayada@gmail.com</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Penilaian & Catatan -->
                    <div class="bg-white p-6 rounded-lg shadow-md mt-6 text-center">
                        <!-- Bintang Penilaian -->
                        <div id="rating" class="flex justify-center space-x-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="cursor-pointer text-gray-300 text-4xl" data-value="{{ $i }}">★</span>
                            @endfor
                        </div>

                        <!-- Catatan -->
                        <div class="mt-4">
                            <label class="font-bold text-lg">Catatan:</label>
                            <textarea id="catatan" class="w-full mt-2 p-2 border rounded-md"
                                placeholder="Catatan untuk mitra"></textarea>
                        </div>

                        <!-- Tombol Tambah -->
                        <button id="submitBtn"
                            class="mt-4 bg-orange-500 text-white px-4 py-2 rounded-lg">Tambah</button>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center mt-4">
                        <button class="px-3 py-1 bg-orange-500 text-white rounded-md">1</button>
                        <button class="px-3 py-1 bg-gray-300 rounded-md ml-2">2</button>
                        <button class="px-3 py-1 bg-gray-300 rounded-md ml-2">3</button>
                        <button class="px-3 py-1 bg-gray-300 rounded-md ml-2">4</button>
                        <button class="px-3 py-1 bg-gray-300 rounded-md ml-2">5</button>
                    </div>
                </div>
        </div>

        <script>
            // JavaScript untuk sistem rating bintang
            const stars = document.querySelectorAll("#rating span");
            let rating = 0;

            stars.forEach(star => {
                star.addEventListener("click", function () {
                    rating = this.getAttribute("data-value");
                    stars.forEach(s => s.classList.remove("text-yellow-400"));
                    for (let i = 0; i < rating; i++) {
                        stars[i].classList.add("text-yellow-400");
                    }
                });
            });

            // Event listener untuk tombol "Tambah"
            document.getElementById("submitBtn").addEventListener("click", function () {
                const catatan = document.getElementById("catatan").value;
                alert(`Rating: ${rating}\nCatatan: ${catatan}`);
                // Di sini bisa dikirim ke backend dengan AJAX atau form submission
            });
        </script>
        </main>
    </div>
    </div>
</body>

</html>