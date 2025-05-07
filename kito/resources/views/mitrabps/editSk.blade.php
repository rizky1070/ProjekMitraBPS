<!-- resources/views/mitrabps/editSk.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Keep the existing head section -->
    <title>Generate SK untuk Semua Mitra</title>
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
            <h1 class="text-2xl font-bold mb-4">Surat Kerja untuk Semua Mitra</h1>
        </div>
    
        <!-- Form untuk upload file dan mengedit template -->
        <div class="items-center justify-between m-4 bg-white p-4 rounded-lg shadow-md">
            <h1 class="text-gray-900 text-xl font-bold mb-1">
                Detail Survei {{$survei->nama_survei}}
            </h1>
            <div class="flex gap-4 border-gray-400 border-b-2 px-1">
                <h2 class="text-lg font-semibold text-gray-800 my-1">Jumlah Mitra : </h2>
                <h2 class="text-lg text-gray-800 my-1 ml-auto"> {{$survei->mitraSurvei->count()}} mitra</h2>  
            </div>
            
            <h1 class="text-gray-900 text-xl font-bold mt-4">
                Form SK untuk Semua Mitra pada Survei {{$survei->nama_survei}}
            </h1>
            <div class="flex flex-col md:flex-row w-full gap-4">
                <form action="{{ route('editSk', ['id_survei' => $survei->id_survei]) }}" method="POST" enctype="multipart/form-data" class="w-full">
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
                                    <span id="file-label" class="font-semibold text-gray-800 pr-8">Pilih File Template :</span>
                                    <span id="file-name" class="text-gray-500 pr-3 truncate max-w-xs">Belum ada file dipilih</span>
                                </div>
                            </div>

                            <!-- Input Nomor SK -->
                            <div class="flex justify-between items-center w-full border-gray-400 border-b-2 py-1">
                                <label for="nomor_sk" class="font-semibold text-gray-800 pr-8">Nomor SK :</label>
                                <input type="text" name="nomor_sk" required class="text-right border-0 border-gray-300 focus:outline-none focus:border-blue-500" placeholder="Masukkan Nomor SK" style="width: 300px;">
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="space-y-4">
                            <!-- Input Nama -->
                            <div class="flex justify-between items-center w-full border-gray-400 border-b-2 py-1">
                                <label for="nama" class="font-semibold text-gray-800 pr-8">Nama Pihak Pertama :</label>
                                <input type="text" name="nama" required class="text-right border-0 border-gray-300 focus:outline-none focus:border-blue-500" placeholder="Masukkan Nama Pihak Pertama" style="width: 300px;">
                            </div>

                            <!-- Input Denda -->
                            <div class="flex justify-between items-center w-full border-gray-400 border-b-2 py-1">
                                <label for="denda" class="font-semibold text-gray-800 pr-8">Denda :</label>
                                <input type="number" name="denda" required class="text-right border-0 border-gray-300 focus:outline-none focus:border-blue-500" placeholder="Masukkan Nilai Denda" style="width: 300px;">
                            </div>
                        </div>
                    </div>

                    <!-- Input tersembunyi -->
                    <input type="hidden" name="id_survei" value="{{ $survei->id_survei }}">

                    <!-- Tombol Submit -->
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-1 bg-orange text-black rounded-md">Generate SK untuk Semua Mitra</button>
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