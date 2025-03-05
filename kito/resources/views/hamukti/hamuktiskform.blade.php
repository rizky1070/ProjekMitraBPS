<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Tambah SK</title>
</head>

<!-- component -->

<body>

    <a href="/hamuktisk" class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Form SK</h2>

            <form action="{{ route('hamuktiskform.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- tanggal -->
                <div class="flex items-center space-x-4">

                    <div>
                        <label for="presensi-date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                        <div class="relative">
                            <div class=" absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </div>
                            <input datepicker id="default-datepicker" name="tanggal" type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 "
                                placeholder="Pilih tgl" required>
                        </div>

                    </div>
                </div>


                <!-- No SK -->

                <div class="mt-4 block text-sm font-medium text-gray-700">
                    Nomor SK
                    <div class="flex items-center gap-x-2">
                        <div class="flex-1">
                            <input type="text" id="nosurat" name="nosurat" readonly
                                class="w-full mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block"
                                required placeholder="no surat">
                        </div>
                        <p class="text-2xl">/</p>
                        <div class="flex-1">
                            <input type="text" id="kodekab" name="kodekab" value="3516"
                                class="w-full mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block"
                                readonly required>
                        </div>
                    </div>

                    <div class="flex items-center gap-x-2 mt-4">
                        <div class="flex-1">
                            <select id="fungsi" name="fungsi"
                                class="w-full mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                                <option value="" disabled selected>fungsi</option>
                                <option value="FUNGS">FUNGSIONAL</option>
                                <option value="UMUM">UMUM</option>
                            </select>
                        </div>
                        <p class="text-2xl">_</p>
                        <div class="flex-1">
                            <div class="flex-1">
                                <input type="text" id="bulan" name="bulan"
                                    class="w-full mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block"
                                    readonly value="{{ $bulan }}" placeholder="tahun">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-x-2 mt-4 w-1/2">
                        <div class="flex-1">
                            <input type="text" id="tahun" name="tahun"
                                class="w-full mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block"
                                readonly value="{{ $tahun }}" placeholder="tahun">
                        </div>

                    </div>
                </div>




                <!-- Uraian -->
                <div class="mt-4">
                    <label for="uraian" class="block text-sm font-medium text-gray-700">Uraian</label>
                    <input type="text" id="uraian" name="perihal" placeholder="Uraian"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                </div>




                <!-- Dokumen -->
                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Unggah
                        dokumen</label>
                    <input name="dokumen"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        id="file_input" type="file">

                    <!-- Tempat menampilkan nama file -->
                    <p id="file-name" class="mt-2 text-sm text-gray-500 dark:text-gray-400"></p>
                </div>

                <!-- Submit button -->
                <div class="mt-6">
                    <button type="submit"
                        class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Main modal Tambah Instansi -->
    <div id="modalTambahInstansi" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex justify-center items-center w-full h-full px-4 ">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-800 opacity-50"></div>

        <!-- Modal content -->
        <div
            class="relative p-4 w-full max-w-2xl max-h-full bg-white rounded-lg shadow dark:bg-gray-700 mx-4 md:mx-auto">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Tambah Instansi
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="modalTambahInstansi">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4 max-h-96 overflow-y-auto">
                <!-- Form Tambah Instansi -->

                <form id="formTambahInstansi" action="{{ route('instansi.store') }}" method="POST">
                    @csrf
                    <!-- Nama Singkat -->
                    <div class="mb-4">
                        <label for="namasingkat"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Singkat</label>
                        <input type="text" id="namasingkat" name="namasingkat"
                            class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="mb-4">
                        <label for="namalengkap"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Lengkap</label>
                        <input type="text" id="namalengkap" name="namalengkap"
                            class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button type="button" id="cancelButton" data-modal-hide="modalTambahInstansi"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                        <button type="submit"
                            class="py-2.5 px-5 ms-auto text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="modalNotification" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 relative z-10">
            <h2 class="text-lg font-semibold mb-2" id="modalTitle"></h2>
            <p id="modalMessage" class="mb-4"></p>
            <button id="closeModal" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Close</button>
        </div>
    </div>

</body>


<script>
    // Mendapatkan elemen select dan input
    const fungsiSelect = document.getElementById('fungsi');
    const nosuratInput = document.getElementById('nosurat');

    // Mengambil nomor transaksi, bulan, dan tahun dari controller
    const nomorTransaksi = @json($nomorTransaksi); // Mengambil data nomor transaksi dari controller
    const bulan = '{{ $bulan }}'; // Mengambil bulan dalam format Romawi
    const tahun = '{{ $tahun }}'; // Mengambil tahun dari controller

    // Menambahkan event listener untuk menangani perubahan pada select
    fungsiSelect.addEventListener('change', function() {
        // Mendapatkan nilai fungsi yang dipilih
        const selectedFungsi = this.value;

        // Mengatur nilai input nosurat berdasarkan fungsi yang dipilih
        if (selectedFungsi && nomorTransaksi[selectedFungsi]) {
            nosuratInput.value = `${nomorTransaksi[selectedFungsi]}`;
        } else {
            nosuratInput.value = ''; // Kosongkan input jika tidak ada fungsi yang dipilih
        }
    });

    // dokumen
    const fileInput = document.getElementById('file_input');
    const fileNameDisplay = document.getElementById('file-name');

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Tampilkan nama file yang dipilih
            fileNameDisplay.textContent = `Dokumen dipilih: ${file.name}`;
        } else {
            // Kosongkan jika tidak ada file yang dipilih
            fileNameDisplay.textContent = '';
        }
    });

    const modal = document.getElementById('modalTambahInstansi');
    const tambahInstansiBtn = document.getElementById('tambahInstansi');
    const cancelButton = document.getElementById('cancelButton');
    const form = document.getElementById('formTambahInstansi');

    // Tampilkan modal ketika tombol "Tambah Instansi" diklik
    tambahInstansiBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
    });

    // Sembunyikan modal ketika tombol "Cancel" diklik
    cancelButton.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Tambahkan event listener untuk menutup modal
    modal.querySelectorAll('[data-modal-hide]').forEach(function(button) {
        button.addEventListener('click', function() {
            modal.classList.add('hidden'); // Menyembunyikan modal dengan menambahkan class 'hidden'
        });
    });
</script>


</html>
