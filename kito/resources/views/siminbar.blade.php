<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css','resources/js/app.js')
    <title>SIMINBAR</title>
</head>
<body>
    <div>
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

        <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-200">
            <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false" class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

            <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
                <div class="flex items-center justify-center mt-8">
                    <div class="flex items-center">
                        <svg class="w-12 h-12" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M364.61 390.213C304.625 450.196 207.37 450.196 147.386 390.213C117.394 360.22 102.398 320.911 102.398 281.6C102.398 242.291 117.394 202.981 147.386 172.989C147.386 230.4 153.6 281.6 230.4 307.2C230.4 256 256 102.4 294.4 76.7999C320 128 334.618 142.997 364.608 172.989C394.601 202.981 409.597 242.291 409.597 281.6C409.597 320.911 394.601 360.22 364.61 390.213Z" fill="#4C51BF" stroke="#4C51BF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M201.694 387.105C231.686 417.098 280.312 417.098 310.305 387.105C325.301 372.109 332.8 352.456 332.8 332.8C332.8 313.144 325.301 293.491 310.305 278.495C295.309 263.498 288 256 275.2 230.4C256 243.2 243.201 320 243.201 345.6C201.694 345.6 179.2 332.8 179.2 332.8C179.2 352.456 186.698 372.109 201.694 387.105Z" fill="white"></path>
                        </svg>

                        <span class="mx-2 text-2xl font-semibold text-white">SIMINBAR</span>
                    </div>
                </div>

                <nav class="mt-10">
                    <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-gray-700 bg-opacity-25" href="#">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>

                        <span class="mx-3">Daftar Barang</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                        href="#">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z">
                            </path>
                        </svg>

                        <span class="mx-3">Inventaris</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                        href="#">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>

                        <span class="mx-3">User</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                        href="#">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>

                        <span class="mx-3">Ketersediaan</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                        href="#">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>

                        <span class="mx-3">Pencarian</span>
                    </a>
                </nav>
            </div>
            <div class="flex flex-col flex-1 overflow-hidden">
                <header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-indigo-600">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </button>

                        <div class="relative mx-4 lg:mx-0">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-500" viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                </svg>
                            </span>

                            <input class="w-32 pl-10 pr-4 rounded-md form-input sm:w-64 focus:border-indigo-600" type="text"
                                placeholder="Search">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div x-data="{ notificationOpen: false }" class="relative">
                            <button @click="notificationOpen = ! notificationOpen"
                                class="flex mx-4 text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15 17H20L18.5951 15.5951C18.2141 15.2141 18 14.6973 18 14.1585V11C18 8.38757 16.3304 6.16509 14 5.34142V5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5V5.34142C7.66962 6.16509 6 8.38757 6 11V14.1585C6 14.6973 5.78595 15.2141 5.40493 15.5951L4 17H9M15 17V18C15 19.6569 13.6569 21 12 21C10.3431 21 9 19.6569 9 18V17M15 17H9"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                </svg>
                            </button>

                            <div x-show="notificationOpen" @click="notificationOpen = false"
                                class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                            <div x-show="notificationOpen"
                                class="absolute right-0 z-10 mt-2 overflow-hidden bg-white rounded-lg shadow-xl w-80"
                                style="width: 20rem; display: none;">
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Sara Salah</span> replied on the <span
                                            class="font-bold text-indigo-400" href="#">Upload Image</span> artical . 2m
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=634&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Slick Net</span> start following you . 45m
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1450297350677-623de575f31c?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Jane Doe</span> Like Your reply on <span
                                            class="font-bold text-indigo-400" href="#">Test with TDD</span> artical . 1h
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=398&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Abigail Bennett</span> start following you . 3h
                                    </p>
                                </a>
                            </div>
                        </div>

                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = ! dropdownOpen"
                                class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none">
                                <img class="object-cover w-full h-full"
                                    src="https://images.unsplash.com/photo-1528892952291-009c663ce843?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=296&amp;q=80"
                                    alt="Your avatar">
                            </button>

                            <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 z-10 w-full h-full"
                                style="display: none;"></div>

                            <div x-show="dropdownOpen"
                                class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
                                style="display: none;">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Profile</a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Products</a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Logout</a>
                            </div>
                        </div>
                    </div>
                </header>
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    <div class="container px-6 py-8 mx-auto">
                        {{-- <h3 class="text-3xl font-medium text-gray-700">Dashboard</h3> --}}

                        {{-- <div class="mt-4">
                            <div class="flex flex-wrap -mx-6">
                                <div class="w-full px-6 sm:w-1/2 xl:w-1/3">
                                    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm">
                                        <div class="p-3 bg-indigo-600 bg-opacity-75 rounded-full">
                                            <svg class="w-8 h-8 text-white" viewBox="0 0 28 30" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M18.2 9.08889C18.2 11.5373 16.3196 13.5222 14 13.5222C11.6804 13.5222 9.79999 11.5373 9.79999 9.08889C9.79999 6.64043 11.6804 4.65556 14 4.65556C16.3196 4.65556 18.2 6.64043 18.2 9.08889Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M25.2 12.0444C25.2 13.6768 23.9464 15 22.4 15C20.8536 15 19.6 13.6768 19.6 12.0444C19.6 10.4121 20.8536 9.08889 22.4 9.08889C23.9464 9.08889 25.2 10.4121 25.2 12.0444Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M19.6 22.3889C19.6 19.1243 17.0927 16.4778 14 16.4778C10.9072 16.4778 8.39999 19.1243 8.39999 22.3889V26.8222H19.6V22.3889Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M8.39999 12.0444C8.39999 13.6768 7.14639 15 5.59999 15C4.05359 15 2.79999 13.6768 2.79999 12.0444C2.79999 10.4121 4.05359 9.08889 5.59999 9.08889C7.14639 9.08889 8.39999 10.4121 8.39999 12.0444Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M22.4 26.8222V22.3889C22.4 20.8312 22.0195 19.3671 21.351 18.0949C21.6863 18.0039 22.0378 17.9556 22.4 17.9556C24.7197 17.9556 26.6 19.9404 26.6 22.3889V26.8222H22.4Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M6.64896 18.0949C5.98058 19.3671 5.59999 20.8312 5.59999 22.3889V26.8222H1.39999V22.3889C1.39999 19.9404 3.2804 17.9556 5.59999 17.9556C5.96219 17.9556 6.31367 18.0039 6.64896 18.0949Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                        </div>

                                        <div class="mx-5">
                                            <h4 class="text-2xl font-semibold text-gray-700">8,282</h4>
                                            <div class="text-gray-500">New Users</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full px-6 mt-6 sm:w-1/2 xl:w-1/3 sm:mt-0">
                                    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm">
                                        <div class="p-3 bg-orange-600 bg-opacity-75 rounded-full">
                                            <svg class="w-8 h-8 text-white" viewBox="0 0 28 28" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M4.19999 1.4C3.4268 1.4 2.79999 2.02681 2.79999 2.8C2.79999 3.57319 3.4268 4.2 4.19999 4.2H5.9069L6.33468 5.91114C6.33917 5.93092 6.34409 5.95055 6.34941 5.97001L8.24953 13.5705L6.99992 14.8201C5.23602 16.584 6.48528 19.6 8.97981 19.6H21C21.7731 19.6 22.4 18.9732 22.4 18.2C22.4 17.4268 21.7731 16.8 21 16.8H8.97983L10.3798 15.4H19.6C20.1303 15.4 20.615 15.1004 20.8521 14.6261L25.0521 6.22609C25.2691 5.79212 25.246 5.27673 24.991 4.86398C24.7357 4.45123 24.2852 4.2 23.8 4.2H8.79308L8.35818 2.46044C8.20238 1.83722 7.64241 1.4 6.99999 1.4H4.19999Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M22.4 23.1C22.4 24.2598 21.4598 25.2 20.3 25.2C19.1403 25.2 18.2 24.2598 18.2 23.1C18.2 21.9402 19.1403 21 20.3 21C21.4598 21 22.4 21.9402 22.4 23.1Z"
                                                    fill="currentColor"></path>
                                                <path
                                                    d="M9.1 25.2C10.2598 25.2 11.2 24.2598 11.2 23.1C11.2 21.9402 10.2598 21 9.1 21C7.9402 21 7 21.9402 7 23.1C7 24.2598 7.9402 25.2 9.1 25.2Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                        </div>

                                        <div class="mx-5">
                                            <h4 class="text-2xl font-semibold text-gray-700">200,521</h4>
                                            <div class="text-gray-500">Total Orders</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full px-6 mt-6 sm:w-1/2 xl:w-1/3 xl:mt-0">
                                    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm">
                                        <div class="p-3 bg-pink-600 bg-opacity-75 rounded-full">
                                            <svg class="w-8 h-8 text-white" viewBox="0 0 28 28" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.99998 11.2H21L22.4 23.8H5.59998L6.99998 11.2Z" fill="currentColor"
                                                    stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                                                <path
                                                    d="M9.79999 8.4C9.79999 6.08041 11.6804 4.2 14 4.2C16.3196 4.2 18.2 6.08041 18.2 8.4V12.6C18.2 14.9197 16.3196 16.8 14 16.8C11.6804 16.8 9.79999 14.9197 9.79999 12.6V8.4Z"
                                                    stroke="currentColor" stroke-width="2"></path>
                                            </svg>
                                        </div>

                                        <div class="mx-5">
                                            <h4 class="text-2xl font-semibold text-gray-700">215,542</h4>
                                            <div class="text-gray-500">Available Products</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- <div class="mt-8">

                        </div> --}}

                        {{-- <section class="bg-gray-50 py-8 antialiased md:py-12"> --}}
                            <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                              <!-- Heading & Filters -->
                              <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                                <div>
                                  {{-- <nav class="flex" aria-label="Breadcrumb">
                                    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                                      <li class="inline-flex items-center">
                                        <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                                          <svg class="me-2.5 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                                          </svg>
                                          Home
                                        </a>
                                      </li>
                                      <li>
                                        <div class="flex items-center">
                                          <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                                          </svg>
                                          <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ms-2">Products</a>
                                        </div>
                                      </li>
                                      <li aria-current="page">
                                        <div class="flex items-center">
                                          <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                                          </svg>
                                          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">Electronics</span>
                                        </div>
                                      </li>
                                    </ol>
                                  </nav> --}}
                                  <h2 class="mt-3 text-xl font-semibold text-gray-900 sm:text-2xl">Daftar Barang</h2>
                                </div>
                                <div class="flex items-center space-x-4">
                                  <button data-modal-toggle="filterModal" data-modal-target="filterModal" type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 sm:w-auto">
                                    <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                      <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                                    </svg>
                                    Filters
                                    <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                    </svg>
                                  </button>
                                  <button id="sortDropdownButton1" data-dropdown-toggle="dropdownSort1" type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 sm:w-auto">
                                    <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                                    </svg>
                                    Sort
                                    <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                    </svg>
                                  </button>
                                  <div id="dropdownSort1" class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow" data-popper-placement="bottom">
                                    <ul class="p-2 text-left text-sm font-medium text-gray-500" aria-labelledby="sortDropdownButton">
                                      <li>
                                        <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"> The most popular </a>
                                      </li>
                                      <li>
                                        <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"> Newest </a>
                                      </li>
                                      <li>
                                        <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"> Increasing price </a>
                                      </li>
                                      <li>
                                        <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"> Decreasing price </a>
                                      </li>
                                      <li>
                                        <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"> No. reviews </a>
                                      </li>
                                      <li>
                                        <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"> Discount % </a>
                                      </li>
                                    </ul>
                                  </div>
                                </div>
                              </div>
                              <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                                  <div class="h-56 w-full">
                                    <a href="#">
                                      <img class="mx-auto h-full" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg" alt="" />
                                      <img class="mx-auto hidden h-full" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg" alt="" />
                                    </a>
                                  </div>
                                  <div class="pt-6">
                                    {{-- <div class="mb-4 flex items-center justify-between gap-4">
                                      <span class="me-2 rounded bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800"> Up to 35% off </span>
                          
                                      <div class="flex items-center justify-end gap-1">
                                        <button type="button" data-tooltip-target="tooltip-quick-look" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                          <span class="sr-only"> Quick look </span>
                                          <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                            <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                          </svg>
                                        </button>
                                        <div id="tooltip-quick-look" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300" data-popper-placement="top">
                                          Quick look
                                          <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                          
                                        <button type="button" data-tooltip-target="tooltip-add-to-favorites" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                          <span class="sr-only"> Add to Favorites </span>
                                          <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                          </svg>
                                        </button>
                                        <div id="tooltip-add-to-favorites" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300" data-popper-placement="top">
                                          Add to favorites
                                          <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                                      </div>
                                    </div> --}}
                          
                                    <a href="#" class="text-lg font-semibold leading-tight text-gray-900 hover:underline">Apple iMac 27", 1TB HDD, Retina 5K Display, M3 Max</a>
                          
                                    {{-- <div class="mt-2 flex items-center gap-2">
                                      <div class="flex items-center">
                                        <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                          <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                        </svg>
                          
                                        <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                          <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                        </svg>
                          
                                        <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                          <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                        </svg>
                          
                                        <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                          <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                        </svg>
                          
                                        <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                          <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                        </svg>
                                      </div>
                          
                                      <p class="text-sm font-medium text-gray-900">5.0</p>
                                      <p class="text-sm font-medium text-gray-500">(455)</p>
                                    </div> --}}
                          
                                    {{-- <ul class="mt-2 flex items-center gap-4">
                                      <li class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500">Fast Delivery</p>
                                      </li>
                          
                                      <li class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                          <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M8 7V6c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1h-1M3 18v-7c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500">Best Price</p>
                                      </li>
                                    </ul> --}}
                          
                                    <div class="mt-4 flex items-center justify-between gap-4">
                                      {{-- <p class="text-2xl font-extrabold leading-tight text-gray-900">$1,699</p> --}}
                          
                                      <button type="button" class="inline-flex items-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-4  focus:ring-primary-300">
                                        {{-- <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg> --}}
                                        Entri
                                      </button>
                                      <button type="button" class="inline-flex items-center rounded-lg bg-red-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4  focus:ring-primary-300">
                                        {{-- <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg> --}}
                                        Delete
                                      </button>
                                      <button type="button" class="inline-flex items-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4  focus:ring-primary-300">
                                        {{-- <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg> --}}
                                        Edit
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="w-full text-center">
                                <button type="button" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100">Show more</button>
                              </div>
                            </div>
                            <!-- Filter modal -->
                            <form action="#" method="get" id="filterModal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-modal w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0 md:h-full">
                              <div class="relative h-full w-full max-w-xl md:h-auto">
                                <!-- Modal content -->
                                <div class="relative rounded-lg bg-white shadow">
                                  <!-- Modal header -->
                                  <div class="flex items-start justify-between rounded-t p-4 md:p-5">
                                    <h3 class="text-lg font-normal text-gray-500">Filters</h3>
                                    <button type="button" class="ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-100 hover:text-gray-900" data-modal-toggle="filterModal">
                                      <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                      </svg>
                                      <span class="sr-only">Close modal</span>
                                    </button>
                                  </div>
                                  <!-- Modal body -->
                                  <div class="px-4 md:px-5">
                                    <div class="mb-4 border-b border-gray-200">
                                      <ul class="-mb-px flex flex-wrap text-center text-sm font-medium" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                                        <li class="mr-1" role="presentation">
                                          <button class="inline-block pb-2 pr-1" id="brand-tab" data-tabs-target="#brand" type="button" role="tab" aria-controls="profile" aria-selected="false">Brand</button>
                                        </li>
                                        <li class="mr-1" role="presentation">
                                          <button class="inline-block px-2 pb-2 hover:border-gray-300 hover:text-gray-600" id="advanced-filers-tab" data-tabs-target="#advanced-filters" type="button" role="tab" aria-controls="advanced-filters" aria-selected="false">Advanced Filters</button>
                                        </li>
                                      </ul>
                                    </div>
                                    <div id="myTabContent">
                                      <div class="grid grid-cols-2 gap-4 md:grid-cols-3" id="brand" role="tabpanel" aria-labelledby="brand-tab">
                                        <div class="space-y-2">
                                          <h5 class="text-lg font-medium uppercase text-black">A</h5>
                          
                                          <div class="flex items-center">
                                            <input id="apple" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="apple" class="ml-2 text-sm font-medium text-gray-900"> Apple (56) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="asus" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="asus" class="ml-2 text-sm font-medium text-gray-900"> Asus (97) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="acer" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="acer" class="ml-2 text-sm font-medium text-gray-900"> Acer (234) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="allview" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="allview" class="ml-2 text-sm font-medium text-gray-900"> Allview (45) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="atari" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="asus" class="ml-2 text-sm font-medium text-gray-900"> Atari (176) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="amd" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="amd" class="ml-2 text-sm font-medium text-gray-900"> AMD (49) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="aruba" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="aruba" class="ml-2 text-sm font-medium text-gray-900"> Aruba (16) </label>
                                          </div>
                                        </div>
                          
                                        <div class="space-y-2">
                                          <h5 class="text-lg font-medium uppercase text-black">B</h5>
                          
                                          <div class="flex items-center">
                                            <input id="beats" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="beats" class="ml-2 text-sm font-medium text-gray-900"> Beats (56) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="bose" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="bose" class="ml-2 text-sm font-medium text-gray-900"> Bose (97) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="benq" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="benq" class="ml-2 text-sm font-medium text-gray-900"> BenQ (45) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="bosch" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="bosch" class="ml-2 text-sm font-medium text-gray-900"> Bosch (176) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="brother" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="brother" class="ml-2 text-sm font-medium text-gray-900"> Brother (176) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="biostar" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="biostar" class="ml-2 text-sm font-medium text-gray-900"> Biostar (49) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="braun" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="braun" class="ml-2 text-sm font-medium text-gray-900"> Braun (16) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="blaupunkt" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="blaupunkt" class="ml-2 text-sm font-medium text-gray-900"> Blaupunkt (45) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="benq2" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="benq2" class="ml-2 text-sm font-medium text-gray-900"> BenQ (23) </label>
                                          </div>
                                        </div>
                          
                                        <div class="space-y-2">
                                          <h5 class="text-lg font-medium uppercase text-black">C</h5>
                          
                                          <div class="flex items-center">
                                            <input id="canon" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="canon" class="ml-2 text-sm font-medium text-gray-900"> Canon (49) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="cisco" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="cisco" class="ml-2 text-sm font-medium text-gray-900"> Cisco (97) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="cowon" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="cowon" class="ml-2 text-sm font-medium text-gray-900"> Cowon (234) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="clevo" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="clevo" class="ml-2 text-sm font-medium text-gray-900"> Clevo (45) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="corsair" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="corsair" class="ml-2 text-sm font-medium text-gray-900"> Corsair (15) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="csl" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="csl" class="ml-2 text-sm font-medium text-gray-900"> Canon (49) </label>
                                          </div>
                                        </div>
                          
                                        <div class="space-y-2">
                                          <h5 class="text-lg font-medium uppercase text-black">D</h5>
                          
                                          <div class="flex items-center">
                                            <input id="dell" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="dell" class="ml-2 text-sm font-medium text-gray-900"> Dell (56) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="dogfish" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="dogfish" class="ml-2 text-sm font-medium text-gray-900"> Dogfish (24) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="dyson" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="dyson" class="ml-2 text-sm font-medium text-gray-900"> Dyson (234) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="dobe" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="dobe" class="ml-2 text-sm font-medium text-gray-900"> Dobe (5) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="digitus" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="digitus" class="ml-2 text-sm font-medium text-gray-900"> Digitus (1) </label>
                                          </div>
                                        </div>
                          
                                        <div class="space-y-2">
                                          <h5 class="text-lg font-medium uppercase text-black">E</h5>
                          
                                          <div class="flex items-center">
                                            <input id="emetec" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="emetec" class="ml-2 text-sm font-medium text-gray-900"> Emetec (56) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="extreme" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="extreme" class="ml-2 text-sm font-medium text-gray-900"> Extreme (10) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="elgato" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="elgato" class="ml-2 text-sm font-medium text-gray-900"> Elgato (234) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="emerson" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="emerson" class="ml-2 text-sm font-medium text-gray-900"> Emerson (45) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="emi" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="emi" class="ml-2 text-sm font-medium text-gray-900"> EMI (176) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="fugoo" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="fugoo" class="ml-2 text-sm font-medium text-gray-900"> Fugoo (49) </label>
                                          </div>
                                        </div>
                          
                                        <div class="space-y-2">
                                          <h5 class="text-lg font-medium uppercase text-black">F</h5>
                          
                                          <div class="flex items-center">
                                            <input id="fujitsu" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="fujitsu" class="ml-2 text-sm font-medium text-gray-900"> Fujitsu (97) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="fitbit" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="fitbit" class="ml-2 text-sm font-medium text-gray-900"> Fitbit (56) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="foxconn" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="foxconn" class="ml-2 text-sm font-medium text-gray-900"> Foxconn (234) </label>
                                          </div>
                          
                                          <div class="flex items-center">
                                            <input id="floston" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                            <label for="floston" class="ml-2 text-sm font-medium text-gray-900"> Floston (45) </label>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                          
                                    <div class="space-y-4" id="advanced-filters" role="tabpanel" aria-labelledby="advanced-filters-tab">
                                      <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                                        <div class="grid grid-cols-2 gap-3">
                                          <div>
                                            <label for="min-price" class="block text-sm font-medium text-gray-900"> Min Price </label>
                                            <input id="min-price" type="range" min="0" max="7000" value="300" step="1" class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200" />
                                          </div>
                          
                                          <div>
                                            <label for="max-price" class="block text-sm font-medium text-gray-900"> Max Price </label>
                                            <input id="max-price" type="range" min="0" max="7000" value="3500" step="1" class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200" />
                                          </div>
                          
                                          <div class="col-span-2 flex items-center justify-between space-x-2">
                                            <input type="number" id="min-price-input" value="300" min="0" max="7000" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 " placeholder="" required />
                          
                                            <div class="shrink-0 text-sm font-medium">to</div>
                          
                                            <input type="number" id="max-price-input" value="3500" min="0" max="7000" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500" placeholder="" required />
                                          </div>
                                        </div>
                          
                                        <div class="space-y-3">
                                          <div>
                                            <label for="min-delivery-time" class="block text-sm font-medium text-gray-900"> Min Delivery Time (Days) </label>
                          
                                            <input id="min-delivery-time" type="range" min="3" max="50" value="30" step="1" class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200" />
                                          </div>
                          
                                          <input type="number" id="min-delivery-time-input" value="30" min="3" max="50" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 " placeholder="" required />
                                        </div>
                                      </div>
                          
                                      <div>
                                        <h6 class="mb-2 text-sm font-medium text-black">Condition</h6>
                          
                                        <ul class="flex w-full items-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-900">
                                          <li class="w-full border-r border-gray-200">
                                            <div class="flex items-center pl-3">
                                              <input id="condition-all" type="radio" value="" name="list-radio" checked class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="condition-all" class="ml-2 w-full py-3 text-sm font-medium text-gray-900"> All </label>
                                            </div>
                                          </li>
                                          <li class="w-full border-r border-gray-200">
                                            <div class="flex items-center pl-3">
                                              <input id="condition-new" type="radio" value="" name="list-radio" class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="condition-new" class="ml-2 w-full py-3 text-sm font-medium text-gray-900"> New </label>
                                            </div>
                                          </li>
                                          <li class="w-full">
                                            <div class="flex items-center pl-3">
                                              <input id="condition-used" type="radio" value="" name="list-radio" class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="condition-used" class="ml-2 w-full py-3 text-sm font-medium text-gray-900"> Used </label>
                                            </div>
                                          </li>
                                        </ul>
                                      </div>
                          
                                      <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                                        <div>
                                          <h6 class="mb-2 text-sm font-medium text-black">Colour</h6>
                                          <div class="space-y-2">
                                            <div class="flex items-center">
                                              <input id="blue" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="blue" class="ml-2 flex items-center text-sm font-medium text-gray-900">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-primary-600"></div>
                                                Blue
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="gray" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="gray" class="ml-2 flex items-center text-sm font-medium text-gray-900">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-gray-400"></div>
                                                Gray
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="green" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="green" class="ml-2 flex items-center text-sm font-medium text-gray-900">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-green-400"></div>
                                                Green
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="pink" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="pink" class="ml-2 flex items-center text-sm font-medium text-gray-900">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-pink-400"></div>
                                                Pink
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="red" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="red" class="ml-2 flex items-center text-sm font-medium text-gray-900">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-red-500"></div>
                                                Red
                                              </label>
                                            </div>
                                          </div>
                                        </div>
                          
                                        <div>
                                          <h6 class="mb-2 text-sm font-medium text-black">Rating</h6>
                                          <div class="space-y-2">
                                            <div class="flex items-center">
                                              <input id="five-stars" type="radio" value="" name="rating" class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="five-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>First star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Second star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Third star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fourth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fifth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="four-stars" type="radio" value="" name="rating" class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="four-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>First star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Second star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Third star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fourth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fifth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="three-stars" type="radio" value="" name="rating" checked class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="three-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>First star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Second star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Third star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fourth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fifth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="two-stars" type="radio" value="" name="rating" class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="two-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>First star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Second star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Third star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fourth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fifth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                              </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="one-star" type="radio" value="" name="rating" class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                                              <label for="one-star" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>First star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Second star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Third star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fourth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <title>Fifth star</title>
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                              </label>
                                            </div>
                                          </div>
                                        </div>
                          
                                        <div>
                                          <h6 class="mb-2 text-sm font-medium text-black">Weight</h6>
                          
                                          <div class="space-y-2">
                                            <div class="flex items-center">
                                              <input id="under-1-kg" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="under-1-kg" class="ml-2 text-sm font-medium text-gray-900"> Under 1 kg </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="1-1-5-kg" type="checkbox" value="" checked class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="1-1-5-kg" class="ml-2 text-sm font-medium text-gray-900"> 1-1,5 kg </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="1-5-2-kg" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="1-5-2-kg" class="ml-2 text-sm font-medium text-gray-900"> 1,5-2 kg </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="2-5-3-kg" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="2-5-3-kg" class="ml-2 text-sm font-medium text-gray-900"> 2,5-3 kg </label>
                                            </div>
                          
                                            <div class="flex items-center">
                                              <input id="over-3-kg" type="checkbox" value="" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500" />
                          
                                              <label for="over-3-kg" class="ml-2 text-sm font-medium text-gray-900"> Over 3 kg </label>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                          
                                      <div>
                                        <h6 class="mb-2 text-sm font-medium text-black">Delivery type</h6>
                          
                                        <ul class="grid grid-cols-2 gap-4">
                                          <li>
                                            <input type="radio" id="delivery-usa" name="delivery" value="delivery-usa" class="peer hidden" checked />
                                            <label for="delivery-usa" class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600 md:p-5">
                                              <div class="block">
                                                <div class="w-full text-lg font-semibold">USA</div>
                                                <div class="w-full">Delivery only for USA</div>
                                              </div>
                                            </label>
                                          </li>
                                          <li>
                                            <input type="radio" id="delivery-europe" name="delivery" value="delivery-europe" class="peer hidden" />
                                            <label for="delivery-europe" class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600 md:p-5">
                                              <div class="block">
                                                <div class="w-full text-lg font-semibold">Europe</div>
                                                <div class="w-full">Delivery only for USA</div>
                                              </div>
                                            </label>
                                          </li>
                                          <li>
                                            <input type="radio" id="delivery-asia" name="delivery" value="delivery-asia" class="peer hidden" checked />
                                            <label for="delivery-asia" class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600 md:p-5">
                                              <div class="block">
                                                <div class="w-full text-lg font-semibold">Asia</div>
                                                <div class="w-full">Delivery only for Asia</div>
                                              </div>
                                            </label>
                                          </li>
                                          <li>
                                            <input type="radio" id="delivery-australia" name="delivery" value="delivery-australia" class="peer hidden" />
                                            <label for="delivery-australia" class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600 md:p-5">
                                              <div class="block">
                                                <div class="w-full text-lg font-semibold">Australia</div>
                                                <div class="w-full">Delivery only for Australia</div>
                                              </div>
                                            </label>
                                          </li>
                                        </ul>
                                      </div>
                                    </div>
                                  </div>
                          
                                  <!-- Modal footer -->
                                  <div class="flex items-center space-x-4 rounded-b p-4 md:p-5">
                                    <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">Show 50 results</button>
                                    <button type="reset" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-200">Reset</button>
                                  </div>
                                </div>
                              </div>
                            </form>
                          {{-- </section> --}}

                        {{-- <div class="flex flex-col mt-8">
                            <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                                <div
                                    class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    No Surat</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Perihal</th>

                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                    <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">B-91/35510/PR.110/2024
                                                    </div>
                                                    <!-- <div class="text-sm leading-5 text-gray-500">john@example.com</div> -->
                                                </td>



                                                <!-- <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 max-h-12 overflow-hidden overflow-ellipsis relative group">
                                                    <div class="text-sm leading-5 text-gray-900 line-clamp-2 group-hover:overflow-visible group-hover:max-h-full group-hover:whitespace-normal" title="detail">
                                                        Undangan Coffee Morning Change Management Agustus, Pembinaan
                                                        Zona Integritas Lanjutan Tahun 2024, serta Pengisian Survei Persepsi
                                                        Penerapan Aplikasi Majapahi
                                                    </div>


                                                    <div class="absolute left-0 top-0 mt-6 hidden w-auto text-sm leading-5 text-gray-900 bg-white p-2 border border-gray-300 rounded-md shadow-md bg-white-border border-gray-300 group-hover:block z-0">
                                                        Undangan Coffee Morning Change Management Agustus, Pembinaan
                                                        Zona Integritas Lanjutan Tahun 2024, serta Pengisian Survei Persepsi
                                                        Penerapan Aplikasi Majapahi
                                                    </div>
                                                </td> -->
                                                <td class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white]">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem" class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50" data-tooltip="LOOK HERE">Undangan Coffee Morning Change Management Agustus, Pembinaan Zona Integritas Lanjutan Tahun 2024, serta Pengisian Survei Persepsi Penerapan Aplikasi Majapahi </div>
                                                    </div>
                                                    <div class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">

                                                        Undangan Coffee Morning Change Management Agustus, Pembinaan Zona Integritas Lanjutan Tahun 2024, serta Pengisian Survei Persepsi Penerapan Aplikasi Majapahi
                                                    </div>
                                                </td>








                                                <td
                                                    class="px-6 py-4 text-sm font-medium leading-5 text-right  border-b border-gray-200 whitespace-nowrap">

                                                    <!-- Ikon untuk Detail -->
                                                    <button class="text-blue-600 hover:text-blue-800 mx-1" title="Detail">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                    <!-- Ikon untuk Edit -->
                                                    <button class="text-yellow-500 hover:text-yellow-700 mx-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <!-- Ikon untuk Hapus -->
                                                    <button class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>




                                            <tr>
                                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                    <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">B-91/35510/PR.110/2024
                                                    </div>

                                                </td>
                                                <td class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white]">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem" class="relative cursor-pointer line-clamp-2 text-xs hover:z-50" data-tooltip="LOOK HERE">Undangan Coffee Morning Change Management Agustus, Pembinaan Zona Integritas Lanjutan Tahun 2024, serta Pengisian Survei Persepsi Penerapan Aplikasi Majapahi </div>
                                                    </div>
                                                    <div class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">

                                                        Undangan Coffee Morning Change Management Agustus, Pembinaan Zona Integritas Lanjutan Tahun 2024, serta Pengisian Survei Persepsi Penerapan Aplikasi Majapahi
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-6 py-4 text-sm font-medium leading-5 text-right  border-b border-gray-200 whitespace-nowrap">

                                                    <!-- Ikon untuk Detail -->
                                                    <button class="text-blue-600 hover:text-blue-800 mx-1" title="Detail">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                    <!-- Ikon untuk Edit -->
                                                    <button class="text-yellow-500 hover:text-yellow-700 mx-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <!-- Ikon untuk Hapus -->
                                                    <button class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div> --}}
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>