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
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    @include('viteall')
    <title>PRISMA</title>
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
                <div class="container px-6 py-8 mx-auto">
                    <h3 class="text-3xl font-medium text-gray-700 ">Daftar Izin Keluar</h3>
                    <h6 class="mt-1"> <span id="today"></span></h6>


                    <div
                        class="grid mb-8 border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 md:mb-12 md:grid-cols-2 lg:grid-cols-3  dark:bg-gray-800 mt-10">
                        @foreach ($pegawaikeluars as $pegawaikeluar)
                            <!-- Figure 1 -->
                            <figure
                                class="flex flex-col items-center justify-center p-8 text-center bg-white border border-gray-200 md:border-r md:w-full dark:bg-gray-800 dark:border-gray-700">
                                <blockquote class="max-w-2xl mx-auto mb-4 text-gray-500 lg:mb-8 dark:text-gray-400">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $pegawaikeluar['namapegawai'] }}</h3>
                                    <p class="my-4">Keperluan: {{ $pegawaikeluar['keperluan'] }}</p>
                                </blockquote>
                                <figcaption class="flex items-center justify-center">
                                    <svg class="rounded-full w-9 h-9" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"
                                            class="text-gray-600 dark:text-gray-300" />
                                        <line x1="12" y1="12" x2="12" y2="6"
                                            class="text-gray-600 dark:text-gray-300" />
                                        <line x1="12" y1="12" x2="16" y2="12"
                                            class="text-gray-600 dark:text-gray-300" />
                                    </svg>

                                    <div class="space-y-0.5 font-medium dark:text-white text-left rtl:text-right ms-3">
                                        <div>Waktu: </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $pegawaikeluar['created_at'] }} - {{ $pegawaikeluar['jamizin'] }}</div>
                                    </div>
                                </figcaption>
                            </figure>
                        @endforeach


                    </div>




                </div>
            </main>
        </div>
    </div>
    </div>

</body>

<script type="text/javascript">
    // Fungsi untuk mendapatkan tanggal dengan format lokal Indonesia dan hari
    function getCurrentDate() {
        const currentDateTime = new Date();

        // Format tanggal dan hari lokal Indonesia
        const options = {
          weekday: 'long', // Menampilkan nama hari
          year: 'numeric', // Menampilkan tahun
          month: 'long', // Menampilkan nama bulan
          day: 'numeric', // Menampilkan tanggal
        };

        const formatter = new Intl.DateTimeFormat('id-ID', options);
        return formatter.format(currentDateTime); // Format sesuai bahasa Indonesia
    }
    
    // Menampilkan tanggal di elemen dengan id 'today'
    window.onload = function() {
      document.getElementById('today').textContent = getCurrentDate();
    };
    
    
    // modal button
    // Trigger search when clicking the search button
    document.getElementById('submitButton').addEventListener('click', function() {
        performSearch();

    });

    // Trigger search when pressing 'Enter' in the search input field
    document.getElementById('searchInput').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission (if inside a form)
            performSearch();
        }
    });



    // Update search result message when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateSearchResultMessage(); // Automatically update message on page load
    });


    function performSearch() {
        const searchValue = document.getElementById('searchInput').value;



        const url = new URL(window.location.href);

        // Set search parameter if it exists
        if (searchValue) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }



        // Redirect with updated query parameters
        window.location.href = url.toString();


    }


    // Function to update the search result message
    function updateSearchResultMessage() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');


        let message = '';

        // If there is a search query, add it to the message
        if (searchQuery) {
            message += `Hasil pencarian untuk: "${searchQuery}"`;
        }


        // Update the message in the DOM
        if (message) {
            document.getElementById('searchResultMessage').textContent = message;
        } else {
            document.getElementById('searchResultMessage').textContent = '';
        }
    }
    
</script>

</html>
