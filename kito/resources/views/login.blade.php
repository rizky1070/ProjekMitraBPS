<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Login</title>
</head>

<body>
    @if ($errors->any())
        <script>
            swal("Error!", "{{ $errors->first() }}", "error");
        </script>
    @endif
    <section
        class="bg-[url('/public/background_login.png')] bg-cover bg-center min-h-screen flex justify-center items-center">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto w-full max-w-md lg:max-w-lg">
            <div>
    <img style="max-width: 300px; min-width: 80px; height: auto; width: 10vw;" src="kanal3516.png" alt="logo">
</div>

            <!-- Logo dan Judul -->
            <div
                class="flex items-center justify-center mb-6 text-lg md:text-3xl font-semibold text-black whitespace-nowrap w-full max-w-md lg:max-w-lg">
                <img class="w-10 h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 mr-3" src="logo aja bps.png" alt="logo">
                BPS Kabupaten Mojokerto
            </div>



            <!-- Card Login -->
            <div class="w-full bg-white bg-opacity-50 rounded-lg shadow-xl border border-gray-200 md:mt-0">
                <div class="p-6 space-y-6">
                    <h1 class="text-2xl font-bold leading-tight tracking-tight text-gray-900 text-center">
                        Log in
                    </h1>
                    <form class="space-y-6" method="POST" action="{{ route('login') }}">
                        @csrf
                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                                Email/Username</label>
                            <input type="text" name="email" id="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3"
                                placeholder="Email/Username" required="">
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3"
                                required="">
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                class="w-full text-white bg-brown-300 hover:bg-brown-400 focus:ring-4 focus:outline-none focus:ring-brown-300 font-medium rounded-lg text-sm px-5 py-3 text-center transition duration-300 ease-in-out">
                                Log in
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</body>

</html>
