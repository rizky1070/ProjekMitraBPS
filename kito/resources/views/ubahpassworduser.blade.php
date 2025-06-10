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
    @include('viteall')
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Ubah Password</title>
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
                        <h2 class="text-2xl font-semibold mb-4">Ubah Password</h2>


                        <form action="{{ route('ubahpassworduser.updatePassword', ['id' => auth()->user()->id]) }}"
                            method="POST">
                            @csrf
                            @method('PUT')



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

                            <!-- password -->
                            <div class="mt-4">
                                <label for="apssword" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" id="password" name="password" placeholder="password"
                                    class="mt-1 p-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required>
                            </div>




                            <!-- Submit button -->
                            <div class="mt-6">
                                <button type="submit"
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



</html>