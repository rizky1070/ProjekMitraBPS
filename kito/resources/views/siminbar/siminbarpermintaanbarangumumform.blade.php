<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Persetujuan Kasubag Umum</title>
</head>

<!-- component -->

<body>

    <a href="/siminbardaftarbarang"
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


            <form action="{{ route('siminbarpermintaanbarangumumform.storeUmum', ['id' => $permintaanbarang['id']]) }}"
                method="POST">
                @csrf
                @method('PUT')
                <!-- Nama -->
                <div class="mt-4">
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="nama" name="nama" value="{{ $permintaanbarang['namauser'] }}"
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
                            placeholder="Cari produk..." onfocus="toggleDropdown()"
                            value="{{ $permintaanbarang['namabarang'] }}" readonly>

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
                    <input type="number" id="stokpermintaan" name="stokpermintaan" placeholder="jumlah tambah"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        min="0" value="{{ $permintaanbarang['stokpermintaan'] }}" required readonly>
                </div>


                <!-- tanggal -->
                <div class="flex items-center space-x-4 mt-4">
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
                                <input id="orderdate" name="orderdate" type="text"
                                    class="bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 "
                                    placeholder="Pilih tgl mulai" value="{{ $permintaanbarang['orderdate'] }}" required
                                    readonly>
                            </div>
                        </div>
                    </div>

                </div>


                <!-- Catatan -->
                <div class="mt-4">
                    <label for="stoktersedia" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <input type="text" id="catatan" name="catatan"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        value="{{ $permintaanbarang['catatan'] }}" readonly>
                </div>





                <!-- Stok tersedia -->
                <div class="mt-4">
                    <label for="stoktersedia" class="block text-sm font-medium text-gray-700">Stok tersedia</label>
                    <input type="number" id="stoktersedia" name="stoktersedia"
                        class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        value="{{ $permintaanbarang['stoktersedia'] }}" required readonly>
                </div>
                <div class="mt-4">
                    <label for="stoktersedia" class="block text-sm font-medium text-gray-700">TTD Anggota</label>
                    <img id="ttd"
                        src="{{ asset('storage/uploads/signatures/' . $permintaanbarang['ttduser']) }}" alt="Image"
                        width="200">
                </div>
                <div class="mt-4">
                    <label for="stoktersedia" class="block text-sm font-medium text-gray-700">Bukti Foto</label>
                    @if (!empty($permintaanbarang['buktifoto']))
                        <img id="bukti"
                            src="{{ asset('storage/uploads/images/' . $permintaanbarang['buktifoto']) }}"
                            alt="Image" width="200">
                    @else
                        <p>-</p>
                    @endif
                </div>
                <div class="mt-4"><label for="stoktersedia" class="block text-sm font-medium text-gray-700">TTD
                        Admin</label>
                    @if (!empty($permintaanbarang['ttdadmin']))
                        <img id="bukti"
                            src="{{ asset('storage/uploads/signatures/' . $permintaanbarang['ttdadmin']) }}"
                            alt="Image" width="200">
                    @else
                        <p>-</p>
                    @endif
                    {{-- <img id="bukti" src="{{ asset('storage/uploads/signatures/' . $permintaanbarang['ttdadmin']) }}" alt="Image" width="200"> --}}
                </div>


                <div class="mt-4">
                    <label for="signature-pad" class="block text-sm font-medium text-gray-700">Tanda Tangan Kabag
                        Umum</label>
                    <div id="signature-pad" class="border-2 border-gray-300 rounded-lg w-full h-72 relative">
                        <canvas id="canvas" class="w-full h-full rounded-lg" name="ttd"></canvas>
                    </div>
                    <button type="button" id="clear"
                        class="mt-2 bg-red-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">Bersihkan</button>
                    <button type="button" id="save"
                        class="mt-2 bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Simpan</button>
                    <input type="hidden" id="ttdumum" name="ttdumum" required>
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
<script>
    // signature pad
    const canvas = document.getElementById('canvas');
    const submitButton = document.getElementById('submit');
    let signatureSaved = false; // Flag to track if signature is saved
    // const signaturePad = new SignaturePad(canvas);

    // Inisialisasi SignaturePad dengan warna biru
    const signaturePad = new SignaturePad(canvas, {
        penColor: 'blue', // Warna tanda tangan diatur menjadi biru
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
        document.getElementById('ttdumum').value = ''; // Reset nilai input
    });

    // Simpan tanda tangan
    document.getElementById('save').addEventListener('click', () => {
        if (signaturePad.isEmpty()) {
            alert("Silakan buat tanda tangan terlebih dahulu.");
        } else {
            // Ambil gambar tanda tangan sebagai data URL
            const dataUrl = signaturePad.toDataURL();
            document.getElementById('ttdumum').value = dataUrl; // Simpan data URL ke input tersembunyi
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
</script>


</html>
