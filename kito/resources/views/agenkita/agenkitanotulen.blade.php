<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <title>Notulen</title>
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
                    <h3 class="text-3xl font-medium text-gray-700">Notulen</h3>

                    <div class="mt-8">
                        <div class=" mb-3">
                            <div class="w-full md:w-1/2 flex flex-col md:flex-row">
                                <div class="flex flex-grow mb-2 md:mb-0">
                                    <input type="text" id="searchInput"
                                        class="w-full border border-gray-300 rounded-l-md p-2"
                                        placeholder="Cari berdasarkan kegiatan">
                                    <button id="searchButton"
                                        class="bg-blue-500 text-white rounded-r-md px-4 py-2">{{ __('Search') }}</button>
                                </div>
                            </div>
                            <!-- Add a placeholder for the search result message -->
                            <p id="searchResultMessage" class="mt-2 text-gray-600"></p>

                            <div class="w-full md:w-1/2 flex items-center space-x-2 mt-3">
                                <a href="/agenkitaformnotulen"
                                    class="bg-green-500 text-white rounded-md px-4 py-2">{{ __('Tambah') }}</a>
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
                                                Nama</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Kegiatan</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Tanggal</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Notulen</th>
                                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white">
                                        @foreach ($notes as $note)
                                            <tr id="{{ $note['id'] }}">
                                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                    <div
                                                        class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                                        {{ $note['name'] }}
                                                    </div>

                                                </td>
                                                <td
                                                    class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem"
                                                            class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                            data-tooltip="LOOK HERE">{{ $note['kegiatan'] }}</div>
                                                    </div>
                                                    <div
                                                        class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">

                                                        {{ $note['kegiatan'] }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem"
                                                            class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                            data-tooltip="LOOK HERE">{{ $note['notulen'] }}</div>
                                                    </div>

                                                </td>


                                                <td
                                                    class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <a href="{{ asset($note['file_path']) }}" download>
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </td>
                                                <td
                                                    class="px-6 py-4 text-sm font-medium leading-5 text-right  border-b border-gray-200 whitespace-nowrap">


                                                    <!-- Ikon untuk Hapus -->
                                                    <form action="{{ route('agenkitanotulen.delete', $note['id']) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                    <!-- Ikon untuk Edit -->
                                                    <a href="{{ route('agenkitaformeditnotulen.editFormNotulen', $note['id']) }}"
                                                        class="text-yellow-500 hover:text-yellow-700 mx-1"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center mt-4">

                        {{ $pagination->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script type="text/javascript">
    // modal button
    // Trigger search when clicking the search button
    document.getElementById('searchButton').addEventListener('click', function() {
        performSearch();
    });

    // Trigger search when pressing 'Enter' in the input field
    document.getElementById('searchInput').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission (if inside a form)
            performSearch();
        }
    });

    function performSearch() {
        const searchValue = document.getElementById('searchInput').value;
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchValue);
        window.location.href = url.toString();
    }

    // Function to update the search result message
    function updateSearchResultMessage() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');

        if (searchQuery) {
            document.getElementById('searchResultMessage').textContent = `Hasil pencarian untuk: "${searchQuery}"`;
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
            fetch(`/presence/${rowId}`) // Ganti URL sesuai dengan endpoint Anda
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Event not found');
                    }
                    return response.json();
                })
                .then(eventData => {
                    // Mengisi modal dengan data event
                    var modalTitle = modal.querySelector('h3');
                    modalTitle.textContent = eventData
                    .kegiatan; // Mengganti judul modal dengan judul event

                    var modalId = modal.querySelector('#id');
                    modalId.textContent = eventData.id;


                    var modalAbsen = modal.querySelector(
                    '#absen'); // Pastikan ada elemen dengan ID ini di modal
                    modalAbsen.textContent = eventData.absen; // Mengganti teks tanggal mulai


                    var modalNama = modal.querySelector(
                    '#nama'); // Pastikan ada elemen dengan ID ini di modal
                    modalNama.textContent = eventData.name; // Mengganti teks tanggal selesai


                    var modalJabatan = modal.querySelector(
                    '#jabatan'); // Pastikan ada elemen dengan ID ini di modal
                    modalJabatan.textContent = eventData.jabatan; // Mengganti teks tanggal mulai

                    var modalSignature = modal.querySelector(
                    '#signature'); // Pastikan ada elemen dengan ID ini di modal
                    var signaturePath = eventData
                    .signature; // Pastikan ada elemen dengan ID ini di modal
                    console.log(signaturePath);
                    // modalSignature.textContent = eventData.signature; // Mengganti teks tanggal mulai

                    // Mengubah src dari elemen <img> dengan path signature
                    // modalSignature.setAttribute('src', '{{ asset('/storage/uploads/signtures') }}/' + signaturePath);
                    modalSignature.setAttribute('src',
                        '{{ asset('storage/uploads/signatures') }}/' + signaturePath);

                    var koordinatLokasi = eventData.lokasi;
                    console.log("koordinat", koordinatLokasi);
                    // Menampilkan peta
                    var map = modal.querySelector('#lokasi');
                    // mapDiv.classList.remove('hidden'); // Menampilkan elemen peta

                    // Memastikan koordinat lokasi terpisah menjadi latitude dan longitude
                    var koordinatLokasi = eventData
                    .lokasi; // Misalkan dalam format "latitude,longitude"
                    var parts = koordinatLokasi.split(
                    ", "); // Memisahkan string menjadi array berdasarkan ", "
                    var lat = Number(parts[0].replace("Lat: ", "")); // Mengonversi ke Number
                    var lon = Number(parts[1].replace("Lon: ", "")); // Mengonversi ke Number


                    console.log("Latitude:", lat); // Output: Latitude: -7.6164526
                    console.log("Longitude:", lon); // Output: Longitude: 112.4410555
                    // Membuat atau memperbarui peta
                    var map = L.map('map').setView([lat, lon],
                    13); // Menetapkan koordinat dan zoom level

                    // Menambahkan layer OpenStreetMap
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Menambahkan marker pada koordinat lokasi
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(`<b>${eventData.kegiatan}</b><br>${koordinatLokasi}`)
                    .openPopup();


                })
                .catch(error => {
                    console.error('Error fetching event data:', error);
                    // Tampilkan pesan kesalahan jika diperlukan
                });

            // Tambahkan event listener untuk menutup modal
            modal.querySelectorAll('[data-modal-hide]').forEach(function(button) {
                button.addEventListener('click', function() {
                    modal.classList.add(
                    'hidden'); // Menyembunyikan modal dengan menambahkan class 'hidden'
                });
            });
        });
    });
</script>

</html>
