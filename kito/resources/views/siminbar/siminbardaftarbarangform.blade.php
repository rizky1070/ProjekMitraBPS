<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('viteall')
        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Tambah Barang</title>
</head>

<!-- component -->

<body>

    <a href="/siminbardaftarbarang" class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Form Daftar Barang</h2>

            <form id="daftarbarangForm" action="{{ URL('/siminbardaftarbarang/store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Nama barang -->
                <div class="mt-4">
                    <label for="namabarang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <input type="text" id="namabarang" name="namabarang" placeholder="Tulis nama barang" class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <!-- Deskripsi -->
                <div class="mt-4">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <input type="text" id="deskripsi" name="deskripsi" placeholder="Tulis deskripsi" class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Stock Tersedia -->
                <div class="mt-4">
                    <label for="stoktersedia" class="block text-sm font-medium text-gray-700">Stock Barang</label>
                    <input type="number" id="stoktersedia" name="stoktersedia" placeholder="Tulis stock tersedia" class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>


                <!-- Gambar -->
                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Unggah gambar</label>

                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" id="dropzone-label" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 bg-center bg-cover">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6" id="upload-text">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG, or GIF</p>
                            </div>
                            <input id="dropzone-file" name="gambar" type="file" class="hidden" accept="image/*" />
                        </label>
                    </div>
                </div>





                <!-- Submit button -->
                <div class="mt-6">
                    <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>

</body>

<script>
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
</script>


</html>