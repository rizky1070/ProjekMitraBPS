<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    @include('viteall')
    <link rel="icon" href="/Logo BPS.png" type="image/png">

    <title>Daftar dan Stok Barang</title>

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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div x-data="{ sidebarOpen: false }" class="flex">
        <x-sidebar></x-sidebar>
        <div class=" flex flex-col flex-1">
            <x-navbar></x-navbar>
            <div class="container mt-5 min-h-screen">
                <h3 class="text-3xl font-medium text-gray-700 m-8">Daftar dan Stok Barang</h3>
                <div class="container px-6  ">
                    <div class="mt-8">
                        <div class=" mb-3">
                            <div class="w-full md:w-1/2 flex flex-col md:flex-row">
                                <div class="flex flex-grow mb-2 md:mb-0">
                                    <input type="text" id="searchInput"
                                        class="w-full border border-gray-300 rounded p-2"
                                        placeholder="Cari berdasarkan barang">
                                </div>
                            </div>

                            <!-- Add a placeholder for the search result message -->
                            <p id="searchResultMessage" class="mt-2 text-gray-600"></p>
                            <div class="w-full md:w-1/2 flex items-center space-x-2 mt-3">
                                <!-- Tombol Submit -->
                                <button id="submitButton"
                                    class=" px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                    Submit
                                </button>

                                <!-- Tombol Reset -->
                                <a id="resetButton" href="/siminbardaftarbarang"
                                    class="ml-2 px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400">
                                    Reset
                                </a>

                                <!-- tombol tambah -->
                                @if (auth()->user()->is_admin)
                                    <a href="{{ URL('siminbardaftarbarangform') }}"
                                        class="bg-green-500 text-white rounded-md px-4 py-2">{{ __('Tambah') }}</a>
                                    <a id="exportButton" href="#" onclick="updateExportPdfLink()"
                                        class="bg-yellow-300 text-white rounded-md px-4 py-2">{{ __('Export Excel') }}</a>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>





                <div class="flex flex-wrap px-6 py-8 mx-auto">
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-5 lg:grid-cols-7 gap-4">
                        @foreach ($barangs as $barang)
                            <div id="{{ $barang['id'] }}"
                                class=" w-full  bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-2">
                                <!-- Menambahkan padding -->
                                <div class="flex justify-end px-2 pt-2"> <!-- Mengurangi padding -->
                                    @if (Auth()->user()->is_admin)
                                        <button id="dropdownButton" data-dropdown-toggle="dropdown-{{ $barang['id'] }}"
                                            class="inline-block text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-1"
                                            type="button"> <!-- Mengurangi padding -->
                                            <span class="sr-only">Open dropdown</span>
                                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 16 3"> <!-- Mengurangi ukuran SVG -->
                                                <path
                                                    d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                                            </svg>
                                        </button>
                                        <!-- Dropdown menu -->
                                        <div id="dropdown-{{ $barang['id'] }}"
                                            class="z-10 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow w-36 dark:bg-gray-700">
                                            <!-- Mengurangi lebar dropdown -->
                                            <ul class="py-1" aria-labelledby="dropdownButton">
                                                <!-- Mengurangi padding -->
                                                <li>
                                                    <a href="/siminbardaftarbarangformedit/{{ $barang['id'] }}"
                                                        class="block px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Edit</a>
                                                </li>
                                                <li>
                                                    <a href="/siminbarinputbarangform/{{ $barang['id'] }}"
                                                        class="block px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Tambah
                                                        Stok</a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('siminbardaftarbarang.delete', $barang['id']) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
                                                            DELETE
                                                        </button>
                                                    </form>
                                                    {{-- <a href="/siminbardaftarbarang/delete/{{ $barang['id'] }}" class="block px-2 py-1 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete</a> --}}
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center pb-4"> <!-- Mengurangi padding bawah -->
                                    @php
                                        // Mendapatkan path gambar
                                        if (is_null($barang['gambar'])) {
                                            $imagePath = 'agenkita.png'; // Menambahkan titik koma
                                        } else {
                                            $imagePath = 'storage/uploads/images/' . $barang['gambar'];
                                        }
                                    @endphp


                                    <img class="w-40 h-40 object-cover mb-2 shadow-lg" src="{{ asset($imagePath) }}"
                                        alt="Gambar Barang" />


                                    <h5 class="text-center mb-1 text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $barang['namabarang'] }}</h5> <!-- Mengurangi ukuran teks -->
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Stok:
                                        {{ $barang['stoktersedia'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-center m-4">

                {{ $pagination->links() }}
            </div>
        </div>

    </div>
</body>

<script type="text/javascript">
    function updateExportPdfLink() {
        const exportUrl = new URL('/siminbardaftarbarang/export', window.location.origin);

        // Menautkan URL ke tombol export PDF
        document.getElementById('exportButton').setAttribute('href', exportUrl.toString());
    };

    // Trigger search when pressing 'Enter' in the search input field
    document.getElementById('searchInput').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission (if inside a form)
            performSearch();
        }
    });


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
