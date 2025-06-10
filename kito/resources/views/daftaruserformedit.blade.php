<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('viteall')
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Edit User</title>
</head>

<!-- component -->

<body>

    <a href="/daftaruser" class="absolute left-0 top-0 bg-gray-700 text-white p-3 m-2 rounded-br-lg hover:bg-gray-900">
        <!-- SVG Ikon Panah Kiri -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class=" bg-gray-100 min-h-screen flex items-center justify-center ">
        <div class="m-8 bg-white p-8 rounded shadow-md max-w-md w-full mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Form Daftar User Edit</h2>

            <form action="{{ route('daftaruserformedit.update', ['id' => $user['id']]) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nama -->
                <div class="mt-4">
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="nama" name="name" placeholder="Nama" class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required readonly value="{{ $user['name'] }}">
                </div>

                <!-- Jabatan -->
                <div class="mt-4">
                    <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                    <input type="text" id="jabatan" name="jabatan" placeholder="Jabatann" class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required value="{{ $user['jabatan'] }}">
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required readonly value="{{ $user['email'] }}">
                </div>

                <!-- Admin -->
                <div class="mt-4">
                    <label for="is_admin" class="block text-sm font-medium text-gray-700">Apakah admin?</label>
                    <select id="is_admin" name="is_admin" class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled {{ is_null($user['is_admin']) ? 'selected' : '' }}>Pilih status</option>
                        <option value="0" {{ $user['is_admin'] == 0 ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $user['is_admin'] == 1 ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>


                <!-- Leader -->
                <div class="mt-4">
                    <label for="is_leader" class="block text-sm font-medium text-gray-700">Apakah leader?</label>
                    <select id="is_leader" name="is_leader" class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled {{ is_null($user['is_leader']) ? 'selected' : '' }}>Pilih status</option>
                        <option value="0" {{ $user['is_leader'] == 0 ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $user['is_leader'] == 1 ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>



                <!-- Hamukti -->
                <div class="mt-4">
                    <label for="is_hamukti" class="block text-sm font-medium text-gray-700">Apakah pengurus hamukti?</label>
                    <select id="is_hamukti" name="is_hamukti" class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled {{ is_null($user['is_hamukti']) ? 'selected' : '' }}>Pilih status</option>
                        <option value="0" {{ $user['is_hamukti'] == 0 ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $user['is_hamukti'] == 1 ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>


                <!-- Is Active -->
                <div class="mt-4">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Apakah akun aktif?</label>
                    <select id="is_active" name="is_active" class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled {{ is_null($user['is_active']) ? 'selected' : '' }}>Pilih status</option>
                        <option value="0" {{ $user['is_active'] == 0 ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $user['is_active'] == 1 ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>


                <!-- username -->
                <div class="mt-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" placeholder="username" class="mt-1 p-2 bg-gray-100 border border-gray-300 text-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required readonly value="{{ $user['username'] }}">
                </div>


                 <!-- username -->
                <div class="mt-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" placeholder="password" class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required  >
                </div>



                <!-- Submit button -->
                <div class="mt-6">
                    <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>


</html>