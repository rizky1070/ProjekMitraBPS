<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Agenda</title>
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

    <div x-data="{ sidebarOpen: false }" class="flex">
        <x-sidebar></x-sidebar>
        <div class=" flex flex-col flex-1">
            <x-navbar></x-navbar>
            <div class="container mt-5 ">
                {{-- For Search --}}



                <div class=" mb-3 m-3">
                    <div class="w-full md:w-1/2 flex items-center space-x-2 m-3 ">
                        @if (auth()->user()->is_leader)
                            <a href="{{ URL('agenkitaformagenda') }}"
                                class="bg-green-500 text-white rounded-md px-4 py-2">{{ __('Tambah') }}</a>
                        @else
                            <a href="{{ URL('agenkitaformagenda') }}"
                                class="hidden bg-green-500 text-white rounded-md px-4 py-2">{{ __('Tambah') }}</a>
                        @endif
                        <button id="todayButton" class="bg-blue-500 text-white rounded-md px-4 py-2">Hari ini</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body ">
                        <div id="calendar"
                            class="m-3 card-body bg-white-500 p-4 border border-black rounded-lg overflow-hidden text-black"
                            style="margin: 20px;height: auto; /* atau tetapkan nilai yang sesuai */ 
                            overflow-y: scroll;">
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Main modal Detail-->
    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex justify-center items-center w-full h-full">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-800 opacity-50"></div>

        <!-- Modal content -->
        <div class="relative p-4 w-full max-w-2xl max-h-full bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">

                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="static-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">

                <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                    <b>Tanggal:</b>
                    <span id="event-startdate"></span>
                    <span>to</span>
                    <span id="event-enddate"></span>
                </div>
                <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                    <b>Waktu:</b>
                    <span id="event-starttime"></span>
                    <span>to</span>
                    <span id="event-endtime"></span>
                </div>
                <div>
                    <img id="image" src="" alt="Image" width="200">
                </div>
                <div>
                    <a id="download" href="" download>
                        <i class="fas fa-download"></i> Download File Agenda
                    </a>
                </div>
                <p id="event_id" class="hidden"></p>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    @if (auth()->user()->is_leader)
                        <button id="edit-button"
                            onclick="window.location.href='/agenkitaformeditagenda/' + document.getElementById('event_id').textContent;"
                            type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Edit
                        </button>
                        <button id="hapus-button" type="button"
                            class="ms-3 text-white bg-red-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Hapus
                        </button>
                    @else
                    @endif

                    <button data-modal-hide="static-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirm-modal" tabindex="-1"
        class="hidden fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-800 bg-opacity-50">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                        delete this product?</h3>
                    <button id="confirm-hapus-button" data-modal-hide="popup-modal" type="button"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yes, I'm sure
                    </button>
                    <button id="cancel-button" data-modal-hide="popup-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                        cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- sukses hapus modal -->
    <div id="delete-success-modal" tabindex="-1"
        class="hidden fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-800 bg-opacity-50">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 p-5 text-center">
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Event deleted successfully</h3>
                <button id="success-close-button" type="button"
                    class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    OK
                </button>
            </div>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var calendarEl = document.getElementById('calendar');

    var events = [];
    var calendar = new FullCalendar.Calendar(calendarEl, {

        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        views: {
            resourceTimelineFifteen: {
                type: "resourceTimeline",
                duration: {
                    days: 15
                },
                buttonText: "15 day",
                slotDuration: {
                    days: 1
                }
            }
        },
        initialView: 'dayGridMonth',
        timeZone: 'UTC',
        events: '/events',
        editable: true,

        // Details The Event
        eventContent: function(info) {
            var eventTitle = info.event.title;
            var eventId = info.event.id; // Menyimpan ID event
            var eventElement = document.createElement('div');

            eventElement.innerHTML = '<span style="cursor: pointer;">ℹ️</span> ' + eventTitle;



            // Menambahkan event listener untuk memunculkan modal
            eventElement.querySelector('span').addEventListener('click', function() {
                var modal = document.getElementById('static-modal');
                modal.classList.remove(
                    'hidden'); // Menampilkan modal dengan menghapus class 'hidden'

                // Mengambil data event dari server berdasarkan eventId
                fetch(`/events/${eventId}`) // Ganti URL sesuai dengan endpoint Anda
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Event not found');
                        }
                        return response.json();
                    })
                    .then(eventData => {
                        // Mengisi modal dengan data event
                        var modalTitle = modal.querySelector('h3');
                        modalTitle.textContent =
                            eventTitle; // Mengganti judul modal dengan judul event

                        var modalId = modal.querySelector('#event_id');
                        modalId.textContent = eventId;


                        var modalDateStart = modal.querySelector(
                            '#event-startdate'); // Pastikan ada elemen dengan ID ini di modal
                        modalDateStart.textContent = eventData
                            .start; // Mengganti teks tanggal mulai

                        var modalDateEnd = modal.querySelector(
                            '#event-enddate'); // Pastikan ada elemen dengan ID ini di modal
                        modalDateEnd.textContent = eventData
                            .end; // Mengganti teks tanggal selesai


                        // var modalImage = modal.querySelector(
                        //     '#image'); // Pastikan ada elemen dengan ID ini di modal
                        // var imagePath = eventData
                        //     .gambar; // Pastikan ada elemen dengan ID ini di modal
                        // modalImage.setAttribute('src',
                        //     '{{ asset('/storage/uploads/images') }}/' + imagePath);


                        // Mendapatkan elemen <img> untuk tanda tangan admin
                        var modalSignature = modal.querySelector('#image');
                        var signatureContainer = modalSignature
                            .parentElement; // Mendapatkan elemen container yang membungkus <img>
                        var signaturePath = eventData
                            .gambar; // Pastikan eventData.ttdadmin benar

                        // Memeriksa apakah signaturePath kosong
                        if (signaturePath && signaturePath.trim() !== "") {
                            // Jika signaturePath ada, ubah src dengan path tanda tangan
                            modalSignature.setAttribute('src',
                                "{{ asset('storage/uploads/images') }}/" + signaturePath);
                            modalSignature.style.display =
                                'block'; // Tampilkan gambar jika ada tanda tangan

                            // Hapus teks "-" jika sebelumnya sudah ada
                            var existingTextSignature = signatureContainer.querySelector(
                                '.text-signature');
                            if (existingTextSignature) {
                                existingTextSignature.remove();
                            }

                        } else {
                            // Jika signaturePath kosong, sembunyikan gambar dan tampilkan tanda "-"
                            modalSignature.style.display = 'none'; // Sembunyikan gambar

                            // Periksa apakah teks "-" sudah ada
                            if (!signatureContainer.querySelector('.text-signature')) {
                                var textSignature = document.createElement('span');
                                textSignature.classList.add(
                                    'text-signature'); // Beri class untuk memudahkan styling
                                textSignature.textContent = '-'; // Isi dengan tanda "-"
                                signatureContainer.appendChild(
                                    textSignature); // Tambahkan teks setelah elemen <img>
                            }
                        }



                        var modalFile = modal.querySelector('#download');
                        var filePath = eventData.dokumen;
                        modalFile.setAttribute('href', '{{ asset('/storage/uploads/docs') }}/' +
                            filePath);

                        var modalTimeStart = modal.querySelector(
                            '#event-starttime'); // Pastikan ada elemen dengan ID ini di modal
                        modalTimeStart.textContent = eventData
                            .time_start; // Mengganti teks tanggal mulai

                        var modalTimeEnd = modal.querySelector(
                            '#event-endtime'); // Pastikan ada elemen dengan ID ini di modal
                        modalTimeEnd.textContent = eventData
                            .time_end; // Mengganti teks tanggal selesai
                    })
                    .catch(error => {
                        console.error('Error fetching event data:', error);
                        // Tampilkan pesan kesalahan jika diperlukan
                    });

                // Tambahkan event listener untuk menutup modal
                modal.querySelectorAll('[data-modal-hide]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        modal.classList.add(
                            'hidden'
                        ); // Menyembunyikan modal dengan menambahkan class 'hidden'
                    });
                });
            });


            return {
                domNodes: [eventElement]
            };
        },

        eventDidMount: function(info) {
            // Menambahkan tooltip untuk menampilkan judul lengkap
            info.el.setAttribute('title', info.event.title);

            // Menambahkan CSS secara dinamis untuk memotong teks
            info.el.style.whiteSpace = 'nowrap'; // Mencegah teks membungkus
            info.el.style.overflow = 'hidden'; // Menyembunyikan teks yang melebihi batas
            info.el.style.textOverflow = 'ellipsis'; // Menambahkan elipsis jika teks terpotong
            info.el.style.width = '100%'; // Sesuaikan lebar event, bisa juga pakai ukuran tetap
            info.el.style.backgroundColor = '#add8e6'; // Warna biru muda
            info.el.style.color = '#000000 !important';
        }



    });


    calendar.render();

    // $('#calendar').css('font-size', '2vw');
    $('.fc-toolbar-title').css('font-size', '3vw'); // Ukuran untuk toolbar
    $('.fc-toolbar').css('font-size', '1.5vw'); // Ukuran untuk toolbar
    // Atur ukuran font untuk semua event (isi kalender)
    // Atur ukuran font untuk tanggal
    $('.fc-day').css('font-size', '1.5vw'); // Ukuran untuk tanggal

    // Atur ukuran font untuk hari dalam sepekan (mis. Senin, Selasa)
    $('.fc-day-header').css('font-size', '1vw'); // Ukuran untuk header hari
    $('.fc-event').css('font-size', '0.5vw'); // Ukuran untuk isi kalender
    $('.fc-event').css('font-color', '#000000'); // Ukuran untuk isi kalender

    document.getElementById("hapus-button").addEventListener("click", function() {
        // Tampilkan modal dengan menghapus class 'hidden'
        var modal = document.getElementById("confirm-modal");
        modal.classList.remove("hidden");

        // Event listener untuk tombol "No, cancel"
        document.querySelectorAll("[data-modal-hide='popup-modal']").forEach(function(button) {
            button.addEventListener("click", function() {
                modal.classList.add("hidden"); // Sembunyikan modal
            });
        });

        // Event listener untuk tombol konfirmasi hapus ("Yes, I'm sure")
        document.getElementById("confirm-hapus-button").addEventListener("click", function() {
            var eventId = document.getElementById('event_id').textContent;
            var xhr = new XMLHttpRequest();

            xhr.open("DELETE", "/schedule/delete/" + eventId, true);

            // Ambil token CSRF dari meta tag
            var csrfToken = document.querySelector("meta[name='csrf-token']").getAttribute('content');

            // Set Header untuk Content-Type dan CSRF Token
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Handle success

                    modal.classList.add("hidden"); // Sembunyikan modal setelah sukses
                    // Optionally reload or remove the deleted event from UI
                    var successModal = document.getElementById("delete-success-modal");
                    successModal.classList.remove("hidden"); // Tampilkan modal sukses

                    // Event listener untuk tombol "OK"
                    document.getElementById("success-close-button").addEventListener("click",
                        function() {
                            successModal.classList.add("hidden");
                            location.reload(); // Reload halaman setelah menutup modal sukses
                        });

                    // Atau, otomatis sembunyikan modal sukses setelah beberapa detik dan reload halaman
                    setTimeout(function() {
                        successModal.classList.add("hidden");
                        location.reload();
                    }, 3000); // Modal sukses akan menghilang setelah 3 detik
                } else if (xhr.readyState == 4) {
                    // Handle error
                    alert("Failed to delete the event");
                }
            };
            xhr.send();
        });
    });
    // Event listener untuk tombol Today
    document.getElementById('todayButton').addEventListener('click', function() {
        calendar.today(); // Mengatur kalender ke hari ini
        calendar.gotoDate(new Date()); // Mengatur tampilan ke hari ini
    });
    document.getElementById('searchButton').addEventListener('click', function() {
        var searchKeywords = document.getElementById('searchInput').value.toLowerCase();
        filterAndDisplayEvents(searchKeywords);
    });
</script>

</html>
