<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('viteall')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.css">
    <script src="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Tambah User</title>
</head>

<!-- component -->

<body>
    @if ($errors->any())
        <script>
            swal("Error!", "{{ $errors->first() }}", "error");
        </script>
    @endif
    <a href="/daftaruser" class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Form Daftar User</h2>

            <form action="{{ route('registerrr') }}" method="POST">
                @csrf
                <!-- Nama -->
                <div class="mt-4">
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="nama" name="name" placeholder="Nama"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                </div>

                <!-- Jabatan -->
                <div class="mt-4">
                    <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                    <input type="text" id="jabatan" name="jabatan" placeholder="Jabatan"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                </div>

                <!-- Admin -->
                <div class="mt-4">
                    <label for="is_admin" class="block text-sm font-medium text-gray-700">Apakah admin?</label>
                    <select id="is_admin" name="is_admin"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled selected>Pilih status</option>
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>

                <!-- Leader -->
                <div class="mt-4">
                    <label for="is_leader" class="block text-sm font-medium text-gray-700">Apakah leader?</label>
                    <select id="is_leader" name="is_leader"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled selected>Pilih status</option>
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>


                <!-- Hamukti -->
                <div class="mt-4">
                    <label for="is_hamukti" class="block text-sm font-medium text-gray-700">Apakah pengurus
                        hamukti?</label>
                    <select id="is_hamukti" name="is_hamukti"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled selected>Pilih status</option>
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>

                <!-- Is active -->
                <div class="mt-4">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Apakah aktif?</label>
                    <select id="is_active" name="is_active"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled selected>Pilih status</option>
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>

                <!-- email -->
                <div class="mt-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" placeholder="username"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                </div>
                <!-- pwd -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" placeholder="username"
                        class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                </div>

                <div class="container bg-white shadow-lg rounded-lg p-6 mt-10">

                    <!-- Gambar Profil (lingkaran) -->
                    <div class="flex justify-center mb-4">
                        <div class="relative">

                            <!-- Gambar Profil yang bisa diubah -->
                            <img id="profileImage" src="/person.png"
                                class="w-32 h-32 object-cover rounded-full border-4 border-gray-200 shadow-lg">

                            <!-- Tombol input gambar -->
                            <label for="inputImage"
                                class="absolute inset-0 flex justify-center items-center bg-black bg-opacity-50 rounded-full cursor-pointer text-white text-lg opacity-0 hover:opacity-100 transition-all">
                                <span class="material-icons">edit</span>
                            </label>
                            <input type="file" id="inputImage" class="hidden" accept="image/*">
                        </div>
                    </div>

                    <!-- Tempat untuk menampilkan gambar yang dipilih untuk dipotong -->
                    <div class="mb-4 flex justify-center">
                        <img id="imagePreview" src="" alt="Gambar untuk dipotong"
                            class="w-32 h-32 object-cover rounded-full border-4 border-gray-200 shadow-md hidden">
                    </div>

                    <!-- Tombol untuk mengambil gambar hasil crop -->
                    <div class="flex justify-between mt-4">
                        <button type="button" id="getCroppedImage"
                            class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Crop
                            Gambar</button>
                        <button type="button" id="resetImage"
                            class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">Reset
                            Gambar</button>
                    </div>

                </div>
                <!-- Input Gambar Hasil Crop (Hidden) -->
                <input type="hidden" id="croppedImage" name="gambar">




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
    let cropper;

    // Menginisialisasi event listener untuk input file
    document.getElementById('inputImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onload = function(event) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = event.target.result;
            imagePreview.classList.remove('hidden'); // Menampilkan gambar setelah dipilih

            // Jika sudah ada cropper, hapus instance sebelumnya
            if (cropper) {
                cropper.destroy();
            }

            // Inisialisasi Cropper.js dengan rasio persegi (1:1)
            cropper = new Cropper(imagePreview, {
                aspectRatio: 1, // Mengatur rasio aspek menjadi 1 (persegi)
                viewMode: 1, // Mode tampilan (1 berarti crop box dapat digeser dan disesuaikan)
                responsive: true, // Membuat responsif di perangkat lain
                cropBoxMovable: true,
                crop(event) {
                    // Bisa menambahkan event crop di sini, misalnya koordinat crop
                }
            });
        };

        // Membaca file yang dipilih
        if (file) {
            reader.readAsDataURL(file);
        }
    });

    // Mengambil gambar hasil crop dan menampilkan di gambar profil yang sama
    document.getElementById('getCroppedImage').addEventListener('click', function() {
        const croppedCanvas = cropper.getCroppedCanvas();

        // Mengubah src gambar profil dengan hasil crop
        const profileImage = document.getElementById('profileImage'); // Mengambil gambar profil
        profileImage.src = croppedCanvas.toDataURL(); // Mengubah src gambar dengan hasil crop

        // Menyimpan hasil crop ke dalam input tersembunyi
        document.getElementById('croppedImage').value = croppedCanvas.toDataURL();


        const imagePreview = document.getElementById('imagePreview');
        imagePreview.classList.add('hidden');
    });

    // Mengatur ulang gambar dan cropper
    document.getElementById('resetImage').addEventListener('click', function() {
        const imagePreview = document.getElementById('imagePreview');
        const profileImage = document.getElementById('profileImage');
        imagePreview.src = '';
        profileImage.src =
            "/person.png"; // Reset ke gambar profil lama
        imagePreview.classList.add('hidden');


        // Reset Cropper jika ada
        if (cropper) {
            cropper.destroy();
        }
    });

    document.getElementById('submitForm').addEventListener('click', function(e) {
        e.preventDefault(); // Mencegah submit otomatis

        const croppedImage = document.getElementById('croppedImage').value; // Cek hasil potongan
        const inputImage = document.getElementById('inputImage').files[0]; // Cek apakah gambar sudah dipilih

        // Cek apakah gambar sudah dipilih tapi belum dipotong
        if (inputImage && !croppedImage) {
            alert('Silakan potong gambar terlebih dahulu!');
            return;
        }

        // Jika tidak ada gambar yang dipilih, set cropped_image ke null sebelum submit
        if (!inputImage) {
            document.getElementById('croppedImage').value = null;
        }

        // Jika sudah dipotong dan siap disubmit, kirim form
        e.target.closest('form').submit();
    });
</script>


</html>
