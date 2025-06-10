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
    @include('viteall')
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Form Izin Keluar</title>
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
                    <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
                        <h2 class="text-2xl font-semibold mb-4">Form Izin Keluar Sementara</h2>


                        <form action="{{ route('izinkeluar.store') }}" method="POST">
                            @csrf

                            <!-- Nama -->
                            <div class="mt-4">
                                <label for="kegiatan" class="block text-sm font-medium text-gray-700">Nama</label>
                                <input type="text" id="nama" name="name" placeholder="Nama"
                                    class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required value="{{ auth()->user()->name }}" readonly>
                            </div>

                            <!-- tanggal -->
                            <div class="flex items-center space-x-4 mt-4">
                                <div id="date-range-picker" date-rangepicker>
                                    <div>
                                        <label for="presensi-date"
                                            class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
                                        <div class="relative">
                                            <div
                                                class=" absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                                </svg>
                                            </div>
                                            <input id="presensi-date" name="tanggalizin" type="text"
                                                class="bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 "
                                                placeholder="Pilih tgl mulai" required readonly>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- jam -->
                            <div class="mt-4 flex space-x-4">
                                <div>
                                    <label for="presensi-time"
                                        class="block mb-2 text-sm font-medium text-gray-700">Waktu Kembali</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input type="time" id="presensi-time" name="jamizin"
                                            class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            min="07:30" max="20:00" required value="07:30" />
                                    </div>
                                </div>

                            </div>
                            <!-- keperluan -->
                            <div class="mt-4">
                                <label for="keperluan" class="block text-sm font-medium text-gray-700">keperluan</label>
                                <input type="text" id="keperluan" name="keperluan" placeholder="keperluan"
                                    class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required>
                            </div>





                            <!-- Submit button -->
                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
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
