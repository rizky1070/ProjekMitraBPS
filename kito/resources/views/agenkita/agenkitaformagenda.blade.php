<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>Tambah Agenda</title>
</head>

<!-- component -->

<body class="bg-gray-100 min-h-screen flex items-center justify-center ">
    <a href="/agenkitaagenda" class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div class="bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
        <h2 class="text-2xl font-semibold mb-4">Form Agenda</h2>

         <form action="{{ route('schedules.store') }}" method="POST" enctype="multipart/form-data"> {{-- URL('/schedule/create') --}}
            @csrf
            <!-- tanggal -->

            <div id="date-range-picker" date-rangepicker class="flex items-center">
                <div class="relative">
                    <div class=" absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    {{-- id input kuganti tanpa pakek - --}}
                    <input id="datepickerrangestart" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pilih tgl mulai" required>
                </div>
                <span class="mx-4 text-gray-500">to</span>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input id="datepickerrangeend" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pilih tgl selesai" required>
                </div>
            </div>

                <!-- jam -->
                <div class="mt-4 flex space-x-4">
                    <div>
                        <label for="start-time" class="block mb-2 text-sm font-medium text-gray-900">Waktu mulai:</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="time" id="start-time" name="timestart" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="00:00" required />
                        </div>
                    </div>
                    <div>
                        <label for="end-time" class="block mb-2 text-sm font-medium text-gray-900">Waktu selesai:</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="time" id="end-time" name="timeend" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="00:00" required />
                        </div>
                    </div>
                </div>


            <!-- Kegiatan -->
            <div class="mt-4">
                <label for="text" class="block text-sm font-medium text-gray-700">Kegiatan</label>
                <input type="text" id="kegiatan" name="kegiatan" placeholder="Tulis kegiatan" class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>

            <!-- Keterangan -->
            <div class="mt-4">
                <label for="text" class="block text-sm font-medium text-gray-700">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" placeholder="Tulis keterangan kegiatan" class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>




            <!-- Dokumen -->
            <div class="mt-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Unggah dokumen</label>
                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" name="dokumen" id="file_input" type="file">

                <!-- Tempat menampilkan nama file -->
                <p id="file-name" class="mt-2 text-sm text-gray-500 dark:text-gray-400"></p>
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

</body>

<script>
    // gambar
    const fileInputGambar = document.getElementById('dropzone-file');
    const dropzoneLabel = document.getElementById('dropzone-label');
    const uploadText = document.getElementById('upload-text');

    fileInputGambar.addEventListener('change', function() {
        const file = this.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function(event) {
                // Ganti background dari dropzone dengan gambar yang diunggah
                dropzoneLabel.style.backgroundImage = `url(${event.target.result})`;
                dropzoneLabel.style.backgroundSize = 'cover';
                dropzoneLabel.style.backgroundPosition = 'center';
                // Sembunyikan teks instruksi setelah gambar diunggah
                uploadText.style.display = 'none';
            };

            reader.readAsDataURL(file);
        } else {
            // Reset jika tidak ada file yang dipilih
            dropzoneLabel.style.backgroundImage = 'none';
            uploadText.style.display = 'flex';
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
</script>