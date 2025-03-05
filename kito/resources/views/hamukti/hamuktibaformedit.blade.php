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
    <title>Edit Berita Acara</title>
</head>

<!-- component -->

<body>

    <a href="/hamuktiba" class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Form BA</h2>

            <form action="{{ route('hamuktiba.update', ['id' => $surat['id']]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                            <input id="default-datepicker" name="tanggal" type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 "
                                placeholder="Pilih tgl" required readonly value="{{ $surat['tanggal'] }}">
                        </div>

                    </div>
                </div>


                <!-- No Kontrak -->

                <div class="mt-4 block text-sm font-medium text-gray-700">
                    Nomor BA
                    <div class="flex items-center gap-x-2">
                        <div class="flex-1">
                            <input type="text" id="nosurat" name="nosurat"
                                class="w-full mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block"
                                required placeholder="no surat" value="{{ $surat['nosurat'] }}">
                        </div>
                        <p class="text-2xl">/</p>
                        <div class="flex-1">
                            <input type="text" id="kodesurat" name="kodesurat"
                                class="w-full mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block"
                                required value="{{ $surat['kodesurat'] }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-x-2 mt-4">
                        <div class="flex-1">
                            <select id="fungsi" name="fungsi"
                                class="w-full mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                                <option value="" disabled selected>fungsi</option>
                                <option value="FUNGS" {{ $surat['fungsi'] == 'FUNGS' ? 'selected' : '' }}>FUNGSIONAL
                                </option>
                                <option value="UMUM" {{ $surat['fungsi'] == 'UMUM' ? 'selected' : '' }}>UMUM</option>
                            </select>
                        </div>
                    </div>
                </div>




                <!-- Uraian -->
                <div class="mt-4">
                    <label for="uraian" class="block text-sm font-medium text-gray-700">Uraian</label>
                    <input type="text" id="uraian" name="uraian" placeholder="Uraian"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required value="{{ $surat['uraian'] }}">
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
</body>


<script>
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


</html>
