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
    <title>Tambah Presensi Admin</title>
</head>

<!-- component -->

<body>

    <a href="/agenkitapresensi"
        class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Form Presensi Admin</h2>

            <form action="{{ route('agenkitaformpresensi.store') }}" method="POST">
                @csrf
                <!-- tanggal -->
                <div class="flex items-center space-x-4">
                    <div id="date-range-picker" date-rangepicker>
                        <div>
                            <label for="presensi-date"
                                class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                            <div class="relative">
                                <div class=" absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <input datepicker id="presensi-date" name="presensidate" type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 "
                                    placeholder="Pilih tgl mulai" required>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- jam -->
                <div class="mt-4 flex space-x-4">
                    <div>
                        <label for="presensi-time" class="block mb-2 text-sm font-medium text-gray-900">Waktu</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="time" id="presensi-time" name="presensitime"
                                class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required value="00:00" />
                        </div>
                    </div>

                </div>



                <!-- Nama -->
                <div class="mt-4">
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
                    <select id="nama" name="nama"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required>
                        <option value="" disabled selected>Pilih Nama Pegawai</option>
                        @foreach ($users as $user)
                            <option value="{{ $user['nama'] }}" data-jabatan="{{ $user['jabatan'] }}">
                                {{ $user['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- kegiatan -->
                <div class="mt-4">
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700">Kegiatan</label>
                    <select id="kegiatan" name="kegiatan"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required>
                        <option value="" disabled selected>Pilih kegiatan</option>
                        @foreach ($presences as $presence)
                            <option value="{{ $presence['id'] }}">{{ $presence['title'] }}</option>
                        @endforeach
                    </select>
                </div>


                <!-- Jabatan -->
                <div class="mt-4">
                    <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                    <input type="text" id="jabatan" name="jabatan"
                        class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required readonly>
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
                    <input type="hidden" id="signature" name="signature">
                </div>



                <!-- lokasi -->
                <div class="mt-4 hidden">
                    <label for="Lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi"
                        class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required readonly>
                </div>


                <!-- Submit button -->
                <div class="mt-6">
                    <button type="submit" id="submit"
                        class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
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
            document.getElementById('signature').value = dataUrl; // Simpan data URL ke input tersembunyi
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

    document.getElementById('nama').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const jabatan = selectedOption.getAttribute('data-jabatan');
        document.getElementById('jabatan').value = jabatan || ''; // Set jabatan or clear if no jabatan
    });
</script>


</html>
