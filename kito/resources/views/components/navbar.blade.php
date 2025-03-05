<header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-indigo-600">
    <div class="flex items-center">
        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
        </button>

        <span class="mx-3 font-bold">Semangat Pagi, {{ auth()->user()->panggilan}} !</span>
        {{-- <span class="mx-3 font-bold">Halo {{ auth()->user()->is_admin }}!</span> --}}

    </div>

    <div class="flex items-center">


        {{-- <div class="relative">
            <button @click="dropdownOpen = ! dropdownOpen"
                class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none">
                <img class="object-cover w-full h-full"
                    src="https://images.unsplash.com/photo-1528892952291-009c663ce843?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=296&amp;q=80"
                    alt="Your avatar">
            </button>



        </div> --}}

        <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <button type="button"
                class="flex items-center justify-center text-sm  rounded-full  md:me-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                data-dropdown-placement="bottom-end">
                <span class="sr-only">Open user menu</span>
                <!-- New User Icon SVG -->
                @php
                    // Mendapatkan path gambar
                    if (is_null(auth()->user()->gambar) || auth()->user()->gambar === '') {
                        $imagePath = '/person.png'; // Menambahkan titik koma
                    } else {
                        $imagePath = 'storage/uploads/images/' . auth()->user()->gambar;
                    }
                   
                @endphp
                <!-- Gambar Profil yang bisa diubah -->
                <img id="profileNavbar" src="{{ asset($imagePath) }}" class="w-10 h-auto object-cover rounded-full  ">

            </button>

            <!-- Dropdown menu -->
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600"
                id="user-dropdown" style="margin-right: 10px; margin-left: 10px;">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900 dark:text-white">{{ Auth()->User()->name }}</span>
                    <span
                        class="block text-sm text-gray-500 truncate dark:text-gray-400">{{ Auth()->User()->jabatan }}</span>
                </div>
                <ul class="py-2" aria-labelledby="user-menu-button">
                    @if (auth()->user()->name === 'Suratno')
                        <li>
                            <a href="/daftaruser"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                Daftar User
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="/ubahpassworduser"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Ubah
                            Password</a>
                    </li>


                    <li>
                        <a href="/ubahprofileuser"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Ubah
                            Profile</a>
                    </li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <!-- Button styled as a link -->
                            <button type="submit"
                                class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white text-left">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>




    </div>
</header>
