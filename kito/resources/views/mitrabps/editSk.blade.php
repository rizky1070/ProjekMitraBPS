<!-- resources/views/upload.blade.php -->

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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <!-- Add jsPDF library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <title>Generate SK</title>
</head>
<body class="h-full bg-gray-200">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200"> 
        <a href="{{ url('/editSurvei/' . $survei->id_survei) }}" 
        class="inline-flex items-center gap-2 px-4 py-2 bg-orange hover:bg-orange-600 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="px-4 pt-4">
            <h1 class="text-2xl font-bold mb-4">Surat Kerja</h1>
        </div>
    
        <!-- Form untuk upload file dan mengedit template -->
        <div class="flex items-center justify-between m-4 bg-white p-4 rounded-lg shadow-md">
            <div class="flex flex-col md:flex-row w-full gap-4">
                <form action="{{ route('editSk', ['id_survei' => $survei->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST" enctype="multipart/form-data" class="w-full">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Kolom Kiri -->
                        <div class="space-y-4">
                            <!-- Input File -->
                            <div class="flex justify-between items-center border-gray-400 border-b-2 py-1 mt-4 relative">
                                <input 
                                    type="file" 
                                    name="file" 
                                    id="file-input" 
                                    required 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                >
                                <div class="flex justify-between items-center w-full pointer-events-none">
                                    <span id="file-label" class="text-gray-700 pr-8">Pilih File :</span>
                                    <span id="file-name" class="text-gray-500 pr-3 truncate max-w-xs">Belum ada file dipilih</span>
                                </div>
                            </div>

                            <!-- Input Nomor SK -->
                            <div class="flex justify-between items-center w-full border-gray-400 border-b-2 py-1">
                                <label for="nomor_sk" class="text-gray-700 pr-8">Nomor SK :</label>
                                <input type="text" name="nomor_sk" required class="text-right border-0 border-gray-300 focus:outline-none focus:border-blue-500" placeholder="Masukkan Nomor SK" style="width: 300px;">
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="space-y-4">
                            <!-- Input Nama -->
                            <div class="flex justify-between items-center w-full border-gray-400 border-b-2 py-1">
                                <label for="nama" class="text-gray-700 pr-8">Nama Pihak Pertama :</label>
                                <input type="text" name="nama" required class="text-right border-0 border-gray-300 focus:outline-none focus:border-blue-500" placeholder="Masukkan Nama Pihak Pertama" style="width: 300px;">
                            </div>

                            <!-- Input Denda -->
                            <div class="flex justify-between items-center w-full border-gray-400 border-b-2 py-1">
                                <label for="denda" class="text-gray-700 pr-8">Denda :</label>
                                <input type="number" name="denda" required class="text-right border-0 border-gray-300 focus:outline-none focus:border-blue-500" placeholder="Masukkan Nilai Denda" style="width: 300px;">
                            </div>
                        </div>
                    </div>

                    <!-- Input tersembunyi -->
                    <input type="hidden" name="id_survei" value="{{ $survei->id_survei }}">
                    <input type="hidden" name="id_mitra" value="{{ $mitra->id_mitra }}">

                    <!-- Tombol Submit -->
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-1 bg-orange text-black rounded-md">Upload and Edit</button>
                    </div>

                    <script>
                        document.getElementById('file-input').addEventListener('change', function(e) {
                            document.getElementById('file-name').textContent = 
                            e.target.files.length ? e.target.files[0].name : "Belum ada file dipilih";
                        });
                    </script>
                </form>
            </div>
        </div>
    </main>


</body>
</html>
