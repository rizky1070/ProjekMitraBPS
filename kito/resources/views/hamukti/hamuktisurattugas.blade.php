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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Surat Tugas</title>
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
                        <h3 class="text-3xl font-medium text-gray-700">Surat Tugas</h3>
                        <div class="mt-8">
                            <div class=" mb-3">
                                <div class="w-full md:w-1/2 flex flex-col md:flex-row">
                                    <div class="flex flex-grow mb-2 md:mb-0">
                                        <input type="text" id="searchInput" class="w-full border border-gray-300 rounded p-2" placeholder="Cari berdasarkan perihal atau nama instansi">
                                    </div>
                                </div>
                                <div class="w-full  flex flex-col md:flex-row mt-3">
                                    <div class="flex flex-grow mb-2 md:mb-0">
                                        <div id="date-range-picker" date-rangepicker class="flex items-center">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                                    </svg>
                                                </div>
                                                <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start">
                                            </div>
                                            <span class="mx-1 text-gray-500">to</span>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                                    </svg>
                                                </div>
                                                <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end">
                                            </div>
                                        </div>
    
    
                                    </div>
    
                                </div>
                                <!-- Add a placeholder for the search result message -->
                                <p id="searchResultMessage" class="mt-2 text-gray-600"></p>
                                <div class="w-full md:w-1/2 flex items-center space-x-2 mt-3">
                                    <!-- Tombol Submit -->
                                    <button id="submitButton" class=" px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                        Submit
                                    </button>
    
                                    <!-- Tombol Reset -->
                                    <a id="resetButton" href="/hamuktisurattugas" class="ml-2 px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400">
                                        Reset
                                    </a>
                                @if (auth()->user()->is_leader)
                                <a href="{{ URL('hamuktisurattugasform') }}" class=" bg-green-500 text-white rounded-md px-4 py-2">{{__('Tambah')}}</a>
                                @else
                                    {{-- <a href="{{ URL('hamuktisuratkeluarform') }}" class=" bg-green-500 text-white rounded-md px-4 py-2">{{__('Tambah')}}</a> --}}
                                @endif
                                </div>
    
                            </div>
                        </div>
    
                        <div class="flex flex-col mt-8">
                            <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                                <div
                                    class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Nomor Surat</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Perihal</th>
                                                    <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Nama</th>
                                                    <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Tanggal</th>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                            </tr>
                                        </thead>
    
                                        <tbody class="bg-white">
                                            @foreach($surattugass as $surattugas)
                                            <tr id="{{ $surattugas['id'] }}">
                                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                    <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">{{ $surattugas['nomorfull'] }}
                                                    </div>
    
                                                </td>
    
                                                <td class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem" class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50" data-tooltip="LOOK HERE">{{ $surattugas['tujuan'] }}</div>
                                                    </div>
                                                    <div class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">
    
                                                        {{ $surattugas['tujuan'] }}
                                                    </div>
                                                </td>
                                                 <td class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem" class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50" data-tooltip="LOOK HERE">{{ $surattugas['tujuan'] }}</div>
                                                    </div>
                                                    <div class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">
    
                                                        {{ $surattugas['tujuan'] }}
                                                    </div>
                                                </td>
                                                <td class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem" class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50" data-tooltip="LOOK HERE">{{ $surattugas['tanggalsurat'] }}</div>
                                                    </div>
    
                                                </td>
    
                                                <td
                                                    class="px-6 py-4 text-sm font-medium leading-5 text-right  border-b border-gray-200 whitespace-nowrap">
                                                    @if (auth()->user()->is_leader)
                                                    <form action="{{ route('hamuktisurattugas.delete', $surattugas['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                    @else
                                                        
                                                    @endif
                                                    <!-- Ikon untuk Detail -->
                                                    <button type="button" class="detail-btn text-blue-600 hover:text-blue-800 mx-1" title="Detail">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
    
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center mt-4">
    
                            {{ $surattugass->links() }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
        </div>
        <!-- Main modal Detail-->
        <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex justify-center items-center w-full h-full">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-800 opacity-50"></div>
    
            <!-- Modal content -->
            <div class="relative p-4 w-full max-w-2xl max-h-full bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="static-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 max-h-96 overflow-y-auto">
    
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Tanggal Surat:</b>
                        <span id="tanggal"></span>
                    </div>
    
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Nomor Surat:</b>
                        <span id="nomorfull"></span>
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Perihal:</b>
                        <span id="perihal"></span>
                    </div>
                    <p id="id" class="hidden"></p>
                    <!-- Modal footer -->
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        @if (auth()->user()->is_leader)
                        <button id="edit-button" onclick="window.location.href='/hamuktisurattugasformedit/' + document.getElementById('id').textContent;" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Edit
                    </button>
                    <button id="hapus-button" type="button" class="ms-3 text-white bg-red-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Hapus
                    </button>
                    @else
                        
                    @endif
                        <button data-modal-hide="static-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="confirm-modal" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-800 bg-opacity-50">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-4 md:p-5 text-center">
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this product?</h3>
                        <button id="confirm-hapus-button" data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Yes, I'm sure
                        </button>
                        <button id="cancel-button" data-modal-hide="popup-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No, cancel</button>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- sukses hapus modal -->
        <div id="delete-success-modal" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-800 bg-opacity-50">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 p-5 text-center">
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Event deleted successfully</h3>
                    <button id="success-close-button" type="button" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script type="text/javascript">
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

    // Trigger search when pressing 'Enter' in the start date input field
    document.getElementById('datepicker-range-start').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch();
        }
    });

    // Trigger search when pressing 'Enter' in the end date input field
    document.getElementById('datepicker-range-end').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch();
        }
    });

    // Update search result message when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateSearchResultMessage(); // Automatically update message on page load
    });


    function performSearch() {
        const searchValue = document.getElementById('searchInput').value;
        const startDate = document.getElementById('datepicker-range-start').value;
        const endDate = document.getElementById('datepicker-range-end').value;
        

        const url = new URL(window.location.href);

        // Set search parameter if it exists
        if (searchValue) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }

        // Set start date parameter if it exists
        if (startDate) {
            url.searchParams.set('start_date', startDate);
        } else {
            url.searchParams.delete('start_date');
        }

        // Set end date parameter if it exists
        if (endDate) {
            url.searchParams.set('end_date', endDate);
        } else {
            url.searchParams.delete('end_date');
        }

        // Redirect with updated query parameters
        window.location.href = url.toString();


    }


    // Function to update the search result message
    function updateSearchResultMessage() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        const startDate = urlParams.get('start_date');
        const endDate = urlParams.get('end_date');

        let message = '';

        // If there is a search query, add it to the message
        if (searchQuery) {
            message += `Hasil pencarian untuk: "${searchQuery}"`;
        }

        // If there is a start date and end date, add them to the message
        if (startDate && endDate) {
            if (message) {
                message += ' dan '; // Connect search query with date range
            }
            message += `rentang tanggal: ${startDate} hingga ${endDate}`;
        }

        // Update the message in the DOM
        if (message) {
            document.getElementById('searchResultMessage').textContent = message;
        } else {
            document.getElementById('searchResultMessage').textContent = '';
        }
    }
    // Pilih semua tombol dengan class 'detail-btn'
    const detailButtons = document.querySelectorAll('.detail-btn');

    // Tambahkan event listener pada setiap tombol
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Ambil id dari elemen baris <tr> yang merupakan parent dari tombol
            var rowId = this.closest('tr').id;
            console.log("ID dari baris yang diklik:", rowId);

            var modal = document.getElementById('static-modal');
            modal.classList.remove('hidden'); // Menampilkan modal dengan menghapus class 'hidden'

            // Mengambil data event dari server berdasarkan eventId
            fetch(`/hamuktisurattugas/${rowId}`) // Ganti URL sesuai dengan endpoint Anda
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Event not found');
                    }
                    return response.json();
                })
                .then(eventData => {
                    // Mengisi modal dengan data event
                    var modalTitle = modal.querySelector('h3');
                    modalTitle.textContent = eventData.tujuan; // Mengganti judul modal dengan judul event

                    var modalId = modal.querySelector('#id');
                    modalId.textContent = eventData.id;

                    var modalDateStart = modal.querySelector('#tanggal'); // Pastikan ada elemen dengan ID ini di modal
                    modalDateStart.textContent = eventData.tanggal; // Mengganti teks tanggal mulai

                    var modalDateEnd = modal.querySelector('#nomorfull'); // Pastikan ada elemen dengan ID ini di modal
                    modalDateEnd.textContent = eventData.nomorfull; // Mengganti teks tanggal selesai

                    var modalTimeStart = modal.querySelector('#perihal'); // Pastikan ada elemen dengan ID ini di modal
                    modalTimeStart.textContent = eventData.tujuan; // Mengganti teks tanggal mulai

                })
                .catch(error => {
                    console.error('Error fetching event data:', error);
                    // Tampilkan pesan kesalahan jika diperlukan
                });

    

            // Tambahkan event listener untuk menutup modal
            modal.querySelectorAll('[data-modal-hide]').forEach(function(button) {
                button.addEventListener('click', function() {
                    modal.classList.add('hidden'); // Menyembunyikan modal dengan menambahkan class 'hidden'
                });
            });
        });
    });
</script>
</html>