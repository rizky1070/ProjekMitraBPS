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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Konfirmasi Kembali ke Kantor</title>
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
                <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">





                    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                        <!-- Modal content -->
                        <div
                            class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5 justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200"
                                class=" mb-3.5 mx-auto">
                                <!-- Pintu -->
                                <rect x="50" y="50" width="60" height="100" fill="#8b4513" stroke="black"
                                    stroke-width="2" />
                                <rect x="110" y="50" width="10" height="100" fill="#d2691e" stroke="black"
                                    stroke-width="2" />

                                <!-- Pegangan Pintu -->
                                <circle cx="115" cy="100" r="3" fill="black" />

                                <!-- Orang -->
                                <circle cx="35" cy="120" r="20" fill="#ffcc99" stroke="black"
                                    stroke-width="2" /> <!-- Kepala -->
                                <rect x="25" y="140" width="20" height="40" fill="#0000ff" stroke="black"
                                    stroke-width="2" /> <!-- Tubuh -->
                                <line x1="25" y1="160" x2="10" y2="180" stroke="black"
                                    stroke-width="2" /> <!-- Tangan kiri -->
                                <line x1="45" y1="160" x2="70" y2="130" stroke="black"
                                    stroke-width="2" /> <!-- Tangan kanan (memegang pintu) -->

                                <!-- Kaki -->
                                <line x1="25" y1="180" x2="25" y2="200" stroke="black"
                                    stroke-width="2" /> <!-- Kaki kiri -->
                                <line x1="45" y1="180" x2="45" y2="200" stroke="black"
                                    stroke-width="2" /> <!-- Kaki kanan -->
                            </svg>


                            <p class="mb-4 text-gray-500 dark:text-gray-300">Apakah Anda sudah kembali ke
                                Kantor?
                            </p>

                            <!-- Form -->
                            <form action="{{ route('izinkeluar.update', ['id' => $izinid]) }}" method="POST">
                                @csrf
                                @method('PUT') <!-- Method spoofing for DELETE -->

                                <!-- Add your item identifier -->

                                <div class="flex justify-center items-center space-x-4">

                                    <button type="submit"
                                        class="py-2 px-3 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-900 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                                        Ya, sudah Boss
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>






                </div>
        </div>
        </main>
    </div>

    </div>

</body>

<script type="text/javascript">
    // Fungsi untuk mendapatkan current date (yyyy-mm-dd)
    function getCurrentDate() {
        const currentDateTime = new Date();
        const currentDate = currentDateTime.toISOString().split('T')[0]; // Format yyyy-mm-dd
        return currentDate;
    }

    // Setelah halaman dimuat, jalankan fungsi ini
    window.onload = function() {
        // Tangkap elemen input
        const startDateInput = document.getElementById('presensi-date');

        // Set nilai input dengan current date
        startDateInput.value = getCurrentDate();




    };
</script>

</html>
