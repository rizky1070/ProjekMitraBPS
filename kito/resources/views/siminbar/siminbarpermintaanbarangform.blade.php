<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Tambah Permintaan Barang</title>
</head>

<!-- component -->

<body>

    <a href="/siminbarpermintaanbarang"
        class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Form Input Barang</h2>


            <form action="{{ route('siminbarpermintaanbarang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Nama -->
                <div class="mt-4">
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="nama" name="nama" value="{{ auth()->user()->name }}"
                        placeholder="Nama"
                        class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required readonly>
                </div>

                <!-- Barang ID-->
                <div class="mt-4">
                    <label for="barang-search" class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                    <div class="relative">
                        <!-- Input untuk pencarian barang -->
                        <input id="barang-search" type="text"
                            class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="Cari produk..." onfocus="toggleDropdown()" readonly>

                        <!-- Dropdown menu -->
                        <div id="dropdown"
                            class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1">
                            <!-- Input untuk pencarian dalam dropdown -->
                            <input id="search" type="text" placeholder="Cari..."
                                class="p-2 border-b border-gray-300 w-full" onkeyup="filterBarangs()">

                            <!-- Daftar produk -->
                            <ul id="barang-list" class="max-h-60 overflow-y-auto" tabindex="-1" role="listbox">
                                <!-- Daftar barang akan diisi melalui JavaScript -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- barang id -->
                <input id="barang_id" name="barang_id" class="hidden">

                <!-- Jumlah order -->
                <div class="mt-4">
                    <label for="jumlahtambah" class="block text-sm font-medium text-gray-700">Jumlah Order</label>
                    <input type="number" id="stokpermintaan" name="stokpermintaan" placeholder="jumlah order"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        min="0" required>
                </div>

                <!-- Stok tersedia -->
                <div class="mt-4">
                    <label for="stoktersedia" class="block text-sm font-medium text-gray-700">Stok tersedia</label>
                    <input type="number" id="stoktersedia" name="stoktersedia"
                        class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required readonly>
                </div>


                          <!-- tanggal -->
                <div class="flex items-center space-x-4 mt-4">
                    <div id="date-range-picker" date-rangepicker>
                        <div>
                            <label for="presensi-date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <input id="orderdate" name="orderdate" type="date" 
                                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                    placeholder="Pilih tgl mulai" required>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Catatan -->
                <div class="mt-4">
                    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <input type="text" id="catatan" name="catatan"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>


                <!-- Gambar -->
                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        for="file_input">Unggah
                        gambar</label>

                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" id="dropzone-label"
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 bg-center bg-cover">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6" id="upload-text">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                        class="font-semibold">Click to upload</span> </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG, or GIF</p>
                            </div>
                            <input id="dropzone-file" name="gambar" type="file" class="hidden"
                                accept="image/*" />
                        </label>
                    </div>
                </div>



                <div class="mt-4">
                    <label for="signature-pad" class="block text-sm font-medium text-gray-700">Tanda Tangan</label>
                    <div id="signature-pad" class="border-2 border-gray-300 rounded-lg w-full h-72 relative">
                        <canvas id="canvas" class="w-full h-full rounded-lg" name="ttd"></canvas>
                    </div>
                    <button type="button" id="clear"
                        class="mt-2 bg-red-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">Bersihkan</button>
                    <button type="button" id="save"
                        class="mt-2 bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Simpan</button>
                    <input type="hidden" id="ttduser" name="ttduser">
                </div>

                <!-- Submit button -->
                <div class="mt-6">
                    <button id="submit" type="submit" id="submit"
                        class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    // fetch data asli
    // let barangs = [];
    let imagePath = 'storage/uploads/images/';
    document.addEventListener('DOMContentLoaded', function() {
        const barangs = @json($barangs); // Mengonversi data PHP ke JavaScript

        // Memanggil fungsi untuk mengisi daftar barang setelah DOM dimuat
        populateBarangList(barangs);
    });

    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden'); // Toggle untuk menampilkan atau menyembunyikan dropdown
    }

    function populateBarangList(barangs) {
        const barangList = document.getElementById('barang-list');
        barangList.innerHTML = ''; // Kosongkan elemen sebelum mengisinya dengan data baru

        // Mengisi daftar barang
        barangs.forEach(barang => {
            const li = document.createElement('li');
            li.className = 'flex items-center p-2 hover:bg-blue-500 hover:text-white cursor-pointer';
            li.onclick = () => selectBarang(barang); // Set klik handler untuk memilih barang
            // var imagePath = 'storage/uploads/images/' + $barang.gambar;
            // Gabungkan imagePath dengan nama file gambar dari barang.gambar
            const fullImagePath = `${imagePath}${barang.gambar}`;
            // Isi setiap item dengan gambar dan nama barang
            const imageHtml = barang.gambar ?
                `<img src="${fullImagePath}" alt="${barang.namabarang}" class="h-8 w-8 rounded-full mr-2">` :
                `<img src="/agenkita.png" alt="${barang.namabarang}" class="h-8 w-8 rounded-full mr-2">`;

            // Isi setiap item dengan gambar dan nama barang
            li.innerHTML = `${imageHtml}<span>${barang.namabarang}</span>`;

            // Tambahkan item ke dalam daftar
            barangList.appendChild(li);
        });
    }

    // Fungsi untuk memfilter barang saat mengetik di input pencarian
    function filterBarangs() {
        const searchInput = document.getElementById('search').value
            .toLowerCase(); // Ambil input pencarian dan ubah ke huruf kecil
        const barangList = document.getElementById('barang-list');
        const items = barangList.getElementsByTagName('li'); // Ambil semua elemen list dalam dropdown

        // Iterasi melalui semua item dan tampilkan atau sembunyikan berdasarkan kecocokan
        Array.from(items).forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                item.style.display = ''; // Tampilkan item jika cocok
            } else {
                item.style.display = 'none'; // Sembunyikan item jika tidak cocok
            }
        });
    }

    // Fungsi untuk memilih barang dan memasukkan data ke dalam input
    function selectBarang(barang) {
        const input = document.getElementById('barang-search'); // Input untuk nama barang
        const stokInput = document.getElementById('stoktersedia'); // Input untuk stok yang tersedia
        const barang_id = document.getElementById('barang_id'); // Input hidden untuk menyimpan ID barang
        const stokPermintaan = document.getElementById('stokpermintaan'); // Input untuk jumlah stok permintaan
        const submitButton = document.getElementById('submit'); // Tombol submit

        // Set nilai input berdasarkan barang yang dipilih
        input.value = barang.namabarang;
        stokInput.value = barang.stoktersedia;
        barang_id.value = barang.id;

        // Periksa jika stok tersedia adalah 0
        if (barang.stoktersedia == 0) {
            // Tampilkan pesan alert
            alert('Anda tidak bisa melakukan submit barang karena stok habis');

            // Disable input stok dan tombol submit
            stokInput.disabled = true;
            stokPermintaan.disabled = true;
            submitButton.disabled = true;
        } else {
            // Aktifkan kembali input stok dan tombol submit jika stok tersedia
            stokInput.disabled = false;
            stokPermintaan.disabled = false;
            submitButton.disabled = false;

            // Set nilai maksimal dan minimal pada input stok permintaan
            stokPermintaan.max = barang.stoktersedia;
            stokPermintaan.min = 1;

            // Tambahkan event listener untuk memastikan nilai yang dimasukkan sesuai dengan min dan max
            stokPermintaan.addEventListener('input', function(e) {
                let currentValue = parseInt(stokPermintaan.value);

                // Cegah pengguna memasukkan angka negatif atau melebihi stok
                if (currentValue > barang.stoktersedia) {
                    // Kosongkan nilai input
                    stokPermintaan.value = '';
                } else if (currentValue < 1 || isNaN(currentValue)) {
                    // Kosongkan nilai input
                    stokPermintaan.value = '';
                }
            });

            // Sembunyikan dropdown setelah barang dipilih
            const dropdown = document.getElementById('dropdown');
            dropdown.classList.add('hidden');
        }
    }

    // Event listener untuk menutup dropdown jika klik di luar
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown');
        const input = document.getElementById('barang-search');

        // Jika klik di luar input atau dropdown, sembunyikan dropdown
        if (!input.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });


    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown');
        const input = document.getElementById('barang-search');
        if (!input.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // gambar

    const fileInputGambar = document.getElementById('dropzone-file');
    const dropzoneLabel = document.getElementById('dropzone-label');
    const uploadText = document.getElementById('upload-text');

    fileInputGambar.addEventListener('change', function() {
        const file = this.files[0];

        if (file && file.type.startsWith('image/')) { // Validasi tipe file
            const reader = new FileReader();

            reader.onload = function(event) {
                // Mengganti background dari dropzone dengan gambar yang diunggah
                dropzoneLabel.style.backgroundImage = `url(${event.target.result})`;
                dropzoneLabel.style.backgroundSize = 'cover'; // Ukuran background cover
                dropzoneLabel.style.backgroundPosition = 'center'; // Posisikan gambar di tengah
                uploadText.style.display = 'none'; // Sembunyikan teks instruksi setelah gambar diunggah
            };

            reader.readAsDataURL(file);
        } else {
            // Reset jika tidak ada file yang dipilih atau bukan gambar
            dropzoneLabel.style.backgroundImage = 'none';
            uploadText.style.display = 'flex';
        }
    });

    // signature pad
    const canvas = document.getElementById('canvas');
    const submitButton = document.getElementById('submit');
    let signatureSaved = false; // Flag to track if signature is saved
    const signaturePad = new SignaturePad(canvas, {
        penColor: 'blue', // Warna pena
        minWidth: 3, // Ketebalan minimum garis
        maxWidth: 6 // Ketebalan maksimum garis
    });

    // Resize canvas on window resize
    function resizeCanvas() {
        // Menyesuaikan ukuran canvas dengan lebar elemen wrapper
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }

    // Panggil fungsi resize saat halaman dimuat
    window.addEventListener("load", resizeCanvas);
    // Panggil fungsi resize saat jendela diubah ukurannya
    window.addEventListener("resize", resizeCanvas);

    // Bersihkan tanda tangan
    document.getElementById('clear').addEventListener('click', () => {
        signaturePad.clear();
        signatureSaved = false; // Mark the signature as saved
        document.getElementById('signature').value = ''; // Reset nilai input
    });

    // Simpan tanda tangan
    document.getElementById('save').addEventListener('click', () => {
        if (signaturePad.isEmpty()) {
            alert("Silakan buat tanda tangan terlebih dahulu.");
        } else {
            // Ambil gambar tanda tangan sebagai data URL
            const dataUrl = signaturePad.toDataURL();
            document.getElementById('ttduser').value = dataUrl; // Simpan data URL ke input tersembunyi
            alert("Tanda tangan disimpan!");
            signatureSaved = true; // Mark the signature as saved
            console.log(dataUrl); // Tampilkan data URL di konsol (opsional)
        }
    });

    // Prevent form submission if signature is not saved
    submitButton.addEventListener('click', function(event) {
        if (!signatureSaved) {
            event.preventDefault(); // Prevent form submission
            alert('Anda harus menyimpan tanda tangan sebelum mengirim form.');
        }
    });

    // Fungsi untuk mendapatkan current date (yyyy-mm-dd)
    function getCurrentDate() {
        const currentDateTime = new Date();
        const currentDate = currentDateTime.toISOString().split('T')[0]; // Format yyyy-mm-dd
        return currentDate;
    }



    // Setelah halaman dimuat, jalankan fungsi ini
    window.onload = function() {
        // Tangkap elemen input
        const startDateInput = document.getElementById('orderdate');

        // Set nilai input dengan current date
        startDateInput.value = getCurrentDate();




    };
</script>


</html>
