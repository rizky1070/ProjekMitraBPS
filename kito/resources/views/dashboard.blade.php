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
    <title>Dashboard 3516</title>
</head>

<body>
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
    <section class="bg-[url('/public/dashboard2.png')] bg-cover bg-center min-h-screen">
<link rel="icon" href="{{ asset('favicon.ico') }}">
        <header>

            <nav class=" px-4 lg:px-6 py-2">
                <div class="grid lg:grid-cols-2">
                    <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                        <div class="flex items-center whitespace-nowrap shadow-lg p-2">
                            <img src="logo aja bps.png" class="mr-3 h-11 sm:h-17" alt="Logo BPS" />
                            <span class="self-center text-xl font-semibold text-white">
                                BPS Kabupaten Mojokerto
                            </span>

                        </div>
                    </div>
                    <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl mt-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-secondary-button :href="route('logout')"
                                onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-secondary-button>
                        </form>
                    </div>
                </div>

            </nav>
        </header>
        <div class="relative isolate px-6 pt-14 lg:px-8">
            <div class="mx-auto max-w-6xl py-16 sm:py-24 lg:py-6">
                <div class="text-center items-center justify-center">
                <img class="mx-auto" style="max-width: 300px; min-width: 80px; height: auto; width: 20vw;" src="kanal3516.png" alt="logo">
                  
                <div class="flex justify-center items-center">
                    <span class="nowrap text-4xl font-bold tracking-tight sm:text-6xl text-white py-3  text-center">
                        KUMPULAN APLIKASI INTERNAL 3516
                    </span>
                </div>

                    <section class="dark:bg-gray-900">
                        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                            <div class="grid gap-8 lg:grid-cols-2">
                                
                                <article
                                    class="p-6 bg-white bg-opacity-50 rounded-lg border border-gray-200 shadow-md transition-all duration-300 flex flex-col items-center justify-center gap-3 hover:bg-opacity-90 hover:bg-gray-300">
                                    <a href="/agenkitapresensi">
                                        <img src="agen kita.png" height="200" width="175" class=""
                                            alt="Logo agenkita" />
                                        <h2
                                            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                            AGEN KITA</h2>
                                    </a>
                                </article>    
                                

                                @if (auth()->user()->is_admin || auth()->user()->is_hamukti)
                                    <article
                                        class="p-6 bg-white bg-opacity-50 rounded-lg border border-gray-200 shadow-md transition-all duration-300 flex flex-col items-center justify-center gap-3 hover:bg-opacity-90 hover:bg-gray-300">
                                        <a href="/hamuktisuratkeluar">
                                            <img src="hamukti.png" height="300" width="175" alt="Logo hamukti" />
                                            <h2
                                                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                                HAMUKTI</h2>
                                        </a>
                                    </article>
                                @endif

                                @if (auth()->user()->is_admin || auth()->user()->is_leader)
                                    <article
                                        class="p-6 bg-white bg-opacity-50 rounded-lg border border-gray-200 shadow-md transition-all duration-300 flex flex-col items-center justify-center gap-3 hover:bg-opacity-90 hover:bg-gray-300">
                                        <a href="/izinkeluar">
                                            <img src="prisma.png" height="300" width="175" alt="Logo hamukti" />
                                            <h2
                                                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                                PRISMA</h2>
                                        </a>
                                    </article>
                                @else
                                    <article
                                        class="p-6 bg-white bg-opacity-50 rounded-lg border border-gray-200 shadow-md transition-all duration-300 flex flex-col items-center justify-center gap-3 hover:bg-opacity-90 hover:bg-gray-300">
                                        <a href="/izinkeluarform">
                                            <img src="prisma.png" height="300" width="175" alt="Logo hamukti" />
                                            <h2
                                                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                                PRISMA</h2>
                                        </a>
                                    </article>
                                @endif
                                
                                <article
                                    class="p-6 bg-white bg-opacity-50 rounded-lg border border-gray-200 shadow-md transition-all duration-300 flex flex-col items-center justify-center gap-3 hover:bg-opacity-90 hover:bg-gray-300">
                                    <a href="https://www.link3516.com" target="_blank">
                                        <img src="supertim.png" height="200" width="175"
                                            alt="Logo BPS Kabupaten Mojokerto" />
                                        <h2
                                            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                            SETAPE</h2>
                                    </a>
                                </article>
                                
                                <article
                                    class="p-6 bg-white bg-opacity-50 rounded-lg border border-gray-200 shadow-md transition-all duration-300 flex flex-col items-center justify-center gap-3 hover:bg-opacity-90 hover:bg-gray-300">
                                    <a href="/siminbarpermintaanbarang">
                                        <img src="siminbar.png" height="300" width="175" alt="Logo siminbar" />
                                        <h2
                                            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                            SIMINBAR</h2>
                                    </a>
                                </article>

                                <article
                                    class="p-6 bg-white bg-opacity-50 rounded-lg border border-gray-200 shadow-md transition-all duration-300 flex flex-col items-center justify-center gap-3 hover:bg-opacity-90 hover:bg-gray-300">
                                    <a href="/daftarsurveibps">
                                        <img src="mitrabps.png" height="300" width="175" alt="Logo siminbar" />
                                        <h2
                                            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                            SIMIT</h2>
                                    </a>
                                </article>
            
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>


    </section>

</body>

</html>
