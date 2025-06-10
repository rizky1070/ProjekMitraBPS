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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.css">
    <script src="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('viteall')
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Ubah Profile</title>
</head>

<body class="h-full">
    <!-- SweetAlert Logic -->
    @if (session('success'))
        <script>
            swal("Success!", "{{ session('success') }}", "success");
        </script>
    @endif

    @if ($errors->any())
        <script>
            swal("Error!", "{{ $errors->first() }}", "error");
        </script>
    @endif
    <!-- component -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
                    <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
                        <h2 class="text-2xl font-semibold mb-4">Ubah Profile</h2>


                        <form action="{{ route('ubahprofileuser.updateProfile', ['id' => auth()->user()->id]) }}"
                            enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('PUT')


                            <div class="container bg-white shadow-lg rounded-lg p-6 mt-10">

                                <!-- Gambar Profil (lingkaran) -->
                                <div class="flex justify-center mb-4">
                                    <div class="relative">
                                        @php
                                            // Mendapatkan path gambar
                                            if (is_null(auth()->user()->gambar) || auth()->user()->gambar === '') {

                                                $imagePath = 'person.png'; // Menambahkan titik koma
                                            } else {
                                                $imagePath = 'storage/uploads/images/' . auth()->user()->gambar;
                                            }
                                        @endphp
                                        <!-- Gambar Profil yang bisa diubah -->
                                        <img id="profileImage" src="{{ asset($imagePath) }}"
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
                            <!-- Nama -->
                            <div class="mt-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                                <input type="text" id="name" name="name" placeholder="name"
                                    class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required value="{{ auth()->user()->name }}">
                            </div>

                            <!-- Email -->
                            <div class="mt-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" placeholder="Email"
                                    class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required readonly value="{{ auth()->user()->email }}">
                            </div>


                            <!-- username -->
                            <div class="mt-4">
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" id="username" name="username" placeholder="username"
                                    class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required readonly value="{{ auth()->user()->username }}">
                            </div>



                            <!-- Jabatan -->
                            <div class="mt-4">
                                <label for="username" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input type="text" id="jabatan" name="jabatan" placeholder="jabatan"
                                    class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required readonly value="{{ auth()->user()->jabatan }}">
                            </div>







                            <!-- Submit button -->
                            <div class="mt-6">
                                <button type="submit" id="submitForm"
                                    class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
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
       // Menyisipkan data gambar dari PHP ke dalam JavaScript
        let gambar = "{{ auth()->user()->gambar ?? '' }}"; // Gunakan nilai gambar atau string kosong jika tidak ada gambar
        
        // Cek ketersediaan gambar menggunakan let
        if (gambar && gambar !== '') {
            // console.log('Gambar Profil Ada:', gambar);
            // Lakukan sesuatu jika gambar ada, misalnya memperbarui UI dengan gambar profil
            document.getElementById('profileImage').src = "{{ asset('storage/uploads/images/'. auth()->user()->gambar) }}" ;
        } else {
            // console.log('Gambar Profil Tidak Ada');
            // Lakukan sesuatu jika gambar tidak ada, misalnya menampilkan gambar default
            document.getElementById('profileImage').src = "/person.png";
        }

       
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
