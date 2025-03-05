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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Permintaan Barang Admin</title>
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
    @if (Auth::user()->is_admin == 1 || Auth::user()->jabatan == 'Kasubag Umum')
        <!-- component -->
        <div x-data="{ sidebarOpen: false }" class="flex h-screen">
            <x-sidebar></x-sidebar>
            <div class="flex flex-col flex-1 overflow-hidden">
                <x-navbar></x-navbar>
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    <div class="container px-6 py-8 mx-auto">
                        <h3 class="text-3xl font-medium text-gray-700">Permintaan Barang</h3>
                        <div class="mt-8">
                            <div class=" mb-3">
                                <div class="w-full md:w-1/2 flex flex-col md:flex-row">
                                    <div class="flex flex-grow mb-2 md:mb-0">
                                        <input type="text" id="searchInput"
                                            class="w-full border border-gray-300 rounded p-2"
                                            placeholder="Cari berdasarkan barang atau nama user">
                                    </div>
                                </div>
                                <div class="w-full  flex flex-col md:flex-row mt-3">
                                    <div class="flex flex-grow mb-2 md:mb-0">
                                        <div id="date-range-picker" date-rangepicker class="flex items-center">
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                                    </svg>
                                                </div>
                                                <input id="datepicker-range-start" name="start" type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                    placeholder="Select date start">
                                            </div>
                                            <span class="mx-1 text-gray-500">to</span>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                                    </svg>
                                                </div>
                                                <input id="datepicker-range-end" name="end" type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                    placeholder="Select date end">
                                            </div>
                                        </div>


                                    </div>

                                </div>
                                <!-- Add a placeholder for the search result message -->
                                <input type="text" id="searchInputExport" class="hidden">
                                <input type="text" id="datepicker-range-start-export" class="hidden">
                                <input type="text" id="datepicker-range-end-export" class="hidden">
                                <p id="searchResultMessage" class="mt-2 text-gray-600"></p>
                                <div class="w-full md:w-1/2 flex items-center space-x-2 mt-3">
                                    <!-- Tombol Submit -->
                                    <button id="submitButton"
                                        class=" px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                        Submit
                                    </button>

                                    <!-- Tombol Reset -->
                                    <a id="resetButton" href="/siminbarpermintaanbarangadmin"
                                        class="ml-2 px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400">
                                        Reset
                                    </a>
                                    <a id="exportButton" href="#" onclick="updateExportPdfLink()"
                                        class="bg-yellow-300 text-white rounded-md px-4 py-2">{{ __('Export PDF') }}</a>
                                    <!-- {{-- @if (auth()->user()->is_leader) --}}
                                {{-- <a href="{{ URL('agenkitaformpresensi/admin') }}" class="bg-green-500 text-white rounded-md px-4 py-2">{{__('Tambah Admin')}}</a> --}}
                                {{-- <a href="{{ URL('hamuktisuratmasukform') }}" class=" bg-green-500 tpext-white rounded-md px-4 py-2">{{__('Tambah')}}</a>
                            @else --}}
                                {{-- <a href="{{ URL('hamuktisuratmasukform') }}" class=" bg-green-500 text-white rounded-md px-4 py-2">{{__('Tambah')}}</a> --}}
                            {{-- @endif --}} -->
                                </div>

                            </div>
                        </div>

                        <!-- Slider -->
                        <div class="flex justify-center mt-4">
                            <div x-data="sliderData()"
                                class="relative max-w-6xl mx-auto p-2 bg-gray-200 rounded-lg shadow-lg mt-5">
                                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Top 10 Barang Sering
                                    Diminta</h2>

                                <div class="flex flex-wrap justify-center gap-2">
                                    <!-- Gunakan gap untuk jarak antar item -->
                                    <template x-for="(slide, index) in slides" :key="index">
                                        <div class="slide flex flex-col justify-center items-center transform transition duration-500 ease-in-out w-72 p-2"
                                            x-show="index >= currentSlide && index < currentSlide + slidesToShow">

                                            <img :src="slide.image" :alt="slide.caption"
                                                class="w-32 h-32 object-cover rounded-lg shadow-lg hover:shadow-2xl transition-transform duration-300 ease-in-out hover:scale-105">

                                            <p x-text="`${index + 1}. ${slide.caption}`"
                                                class="text-center text-lg font-semibold text-gray-700 mt-4"></p>
                                        </div>
                                    </template>
                                </div>

                                <button @click="prevSlide"
                                    :class="{
                                        'bg-gray-900 bg-opacity-60 text-white': !
                                            isPrevDisabled(),
                                        'bg-gray-300 text-gray-500 cursor-not-allowed': isPrevDisabled()
                                    }"
                                    :disabled="isPrevDisabled()"
                                    class="absolute top-1/2 left-4 transform -translate-y-1/2 p-4 rounded-full shadow-lg transition duration-300">
                                    <i class="fas fa-chevron-left text-lg"></i>
                                </button>

                                <button @click="nextSlide"
                                    :class="{
                                        'bg-gray-900 bg-opacity-60 text-white': !
                                            isNextDisabled(),
                                        'bg-gray-300 text-gray-500 cursor-not-allowed': isNextDisabled()
                                    }"
                                    :disabled="isNextDisabled()"
                                    class="absolute top-1/2 right-4 transform -translate-y-1/2 p-4 rounded-full shadow-lg transition duration-300">
                                    <i class="fas fa-chevron-right text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col mt-8">
                            <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                                <div
                                    class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    User</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Barang</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Jumlah Permintaan</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                    Waktu Order</th>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white">
                                            @foreach ($permintaanbarangs as $permintaanbarang)
                                                <tr id="{{ $permintaanbarang['id'] }}">
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                        <div
                                                            class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                                            {{ $permintaanbarang['status'] }}
                                                        </div>

                                                    </td>

                                                    <td
                                                        class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                        <div id="grid" class=" gap-2  p-4 ">
                                                            <div id="gridItem"
                                                                class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                                data-tooltip="LOOK HERE">
                                                                {{ $permintaanbarang['namauser'] }}</div>
                                                        </div>
                                                        <div
                                                            class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">

                                                            {{ $permintaanbarang['namauser'] }}
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                        <div id="grid" class=" gap-2  p-4 ">
                                                            <div id="gridItem"
                                                                class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                                data-tooltip="LOOK HERE">
                                                                {{ $permintaanbarang['namabarang'] }}</div>
                                                        </div>

                                                    </td>
                                                    <td
                                                        class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                        <div id="grid" class=" gap-2  p-4 ">
                                                            <div id="gridItem"
                                                                class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                                data-tooltip="LOOK HERE">
                                                                {{ $permintaanbarang['stokpermintaan'] }}</div>
                                                        </div>

                                                    </td>
                                                    <td
                                                        class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                        <div id="grid" class=" gap-2  p-4 ">
                                                            <div id="gridItem"
                                                                class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                                data-tooltip="LOOK HERE">
                                                                {{ $permintaanbarang['orderdate'] }}</div>
                                                        </div>

                                                    </td>
                                                    <td
                                                        class="px-6 py-4 text-sm font-medium leading-5 text-right  border-b border-gray-200 whitespace-nowrap">
                                                        @if (auth()->user()->is_leader || auth()->user()->email === 'annisarne12@gmail.com')
                                                            <form
                                                                action="{{ route('siminbarpermintaanbarang.delete', $permintaanbarang['id']) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');"
                                                                style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button type="submit"
                                                                    class="text-red-600 hover:text-red-800 mx-1"
                                                                    title="Hapus">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                        @endif
                                                        <!-- Ikon untuk Detail -->
                                                        <button type="button"
                                                            class="detail-btn text-blue-600 hover:text-blue-800 mx-1"
                                                            title="Detail">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center mt-4">

                            {{ $permintaanbarangs->links() }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
        </div>
        <!-- Main modal Detail-->
        <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
            class="hidden fixed inset-0 z-50 flex justify-center items-center w-full h-full">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-800 opacity-50"></div>

            <!-- Modal content -->
            <div class="relative p-4 w-full max-w-2xl max-h-full bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">

                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="static-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 max-h-96 overflow-y-auto">

                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Status:</b>
                        <span id="status"></span>
                    </div>

                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>User:</b>
                        <span id="user"></span>
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Barang:</b>
                        <span id="barang"></span>
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Jumlah Permintaan:</b>
                        <span id="stokpermintaan"></span>
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Waktu Order:</b>
                        <span id="orderdate"></span>
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        <b>Bukti foto:</b>
                        <img id="imagebukti" src="" alt="Image" width="200">

                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Catatan:</b>
                        <span id="catatan"></span>
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400" id="event-id">
                        <b>Stok Tersedia:</b>
                        <span id="stoktersedia"></span>
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        <b>TTD User:</b>
                        <img id="imagettduser" src="" alt="Image" width="200">
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        <b>TTD Admin:</b>
                        <img id="imagettdadmin" src="" alt="Image" width="200">
                    </div>
                    <div class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        <b>TTD Kasubag Umum:</b>
                        <img id="imagettdumum" src="" alt="Image" width="200">
                    </div>
                    <p id="id" class="hidden"></p>
                    <!-- Modal footer -->
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        @if (auth()->user()->is_admin)
                            <button id="edit-button"
                                onclick="window.location.href='/siminbarpermintaanbarangadminform/' + document.getElementById('id').textContent;"
                                type="button"
                                class="hidden text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Tindak Lanjuti
                            </button>

                            <form action="{{ route('siminbarpermintaanbarang.delete', $permintaanbarang['id']) }}" 
                                  method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                
                                <button type="submit"
                                        class="ms-3 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                    Hapus
                                </button>
                            </form>

                        @elseif (auth()->user()->jabatan === 'Kasubag Umum')
                            <!-- Mengganti else dengan elseif -->
                            <button id="umum-button"
                                onclick="window.location.href='/siminbarpermintaanbarangumumform/' + document.getElementById('id').textContent;"
                                type="button"
                                class="hidden text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                TTD Kasubag Umum
                            </button>
                        @endif

                        <button id="eksport-button"
                            onclick="window.location.href='/siminbarpermintaanbarangekspor/' + document.getElementById('id').textContent;"
                            type="button"
                            class="py-2.5 px-5 ms-3 text-white bg-yellow-300 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Eksport
                        </button>
                        <button data-modal-hide="static-modal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancel</button>
                    </div>

                </div>
            </div>
        </div>
        <div id="confirm-modal" tabindex="-1"
            class="hidden fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-800 bg-opacity-50">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <button type="button"
                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="popup-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-4 md:p-5 text-center">
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                            delete this product?</h3>
                        <button id="confirm-hapus-button" data-modal-hide="popup-modal" type="button"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Yes, I'm sure
                        </button>
                        <button id="cancel-button" data-modal-hide="popup-modal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                            cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- sukses hapus modal -->
        <div id="delete-success-modal" tabindex="-1"
            class="hidden fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-800 bg-opacity-50">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 p-5 text-center">
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Event deleted successfully
                    </h3>
                    <button id="success-close-button" type="button"
                        class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        OK
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- Tampilkan pesan error atau arahkan pengguna non-admin -->
        <script>
            alert("Anda tidak memiliki akses ke halaman ini");
            window.history.back();
        </script>
    @endif
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script type="text/javascript">
    // modal button
    // Trigger search when clicking the search button
    document.getElementById('submitButton').addEventListener('click', function() {
        performSearch();

    });

    const topBarangs = @json($topBarangs); // Mengonversi data PHP ke JavaScript
    function sliderData() {
        return {
            currentSlide: 0,
            slides: topBarangs.map(topBarang => ({
                image: topBarang.gambar ? '{{ asset('storage/uploads/images/') }}/' + topBarang.gambar :
                    'agenkita.png',
                caption: topBarang.namabarang
            })),
            slidesToShow: 3, // Default value
            totalSlides: topBarangs.length,

            // Function to update slidesToShow based on screen width
            updateSlidesToShow() {
                if (window.innerWidth < 640) {
                    this.slidesToShow = 2; // 1 item for small screens
                } else if (window.innerWidth < 1024) {
                    this.slidesToShow = 2; // 2 items for medium screens
                } else {
                    this.slidesToShow = 3; // 3 items for large screens
                }
            },
            nextSlide() {
                this.currentSlide = Math.min(this.currentSlide + this.slidesToShow, this.totalSlides - this
                    .slidesToShow);
            },
            prevSlide() {
                this.currentSlide = Math.max(this.currentSlide - this.slidesToShow, 0);
            },

            // Function to check if buttons should be disabled
            isPrevDisabled() {
                return this.currentSlide === 0;
            },
            isNextDisabled() {
                return this.currentSlide >= this.totalSlides - this.slidesToShow;
            },
            // Initialize function
            init() {
                this.updateSlidesToShow(); // Set initial value based on current screen width
                window.addEventListener('resize', () => this.updateSlidesToShow()); // Update on window resize
            }
        };
    }

    // Trigger search when pressing 'Enter' in the search input field
    document.getElementById('searchInput').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission (if inside a form)
            performSearch();
        }
    });

    // Trigger search when pressing 'Enter' in the start date input field
    document.getElementById('datepicker-range-start').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch();
        }
    });

    // Trigger search when pressing 'Enter' in the end date input field
    document.getElementById('datepicker-range-end').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch();
        }
    });

    // Update search result message when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateSearchResultMessage(); // Automatically update message on page load
    });

    function updateExportPdfLink() {
        const searchValue = document.getElementById('searchInputExport').value;
        const startDate = document.getElementById('datepicker-range-start-export').value;
        const endDate = document.getElementById('datepicker-range-end-export').value;
        console.log(startDate)
        console.log(endDate)

        // Membentuk URL untuk export PDF dengan parameter query
        const exportUrl = new URL('/pdf_export_siminbar', window.location.origin);

        if (searchValue) {
            exportUrl.searchParams.set('search', searchValue);
        }
        if (startDate) {
            exportUrl.searchParams.set('start_date', startDate);
        }
        if (endDate) {
            exportUrl.searchParams.set('end_date', endDate);
        }

        // Menautkan URL ke tombol export PDF
        document.getElementById('exportButton').setAttribute('href', exportUrl.toString());
    }

    function performSearch() {
        const searchValue = document.getElementById('searchInput').value;
        const startDate = document.getElementById('datepicker-range-start').value;
        const endDate = document.getElementById('datepicker-range-end').value;


        const url = new URL(window.location.href);

        // Set search parameter if it exists
        if (searchValue) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }

        // Set start date parameter if it exists
        if (startDate) {
            url.searchParams.set('start_date', startDate);
        } else {
            url.searchParams.delete('start_date');
        }

        // Set end date parameter if it exists
        if (endDate) {
            url.searchParams.set('end_date', endDate);
        } else {
            url.searchParams.delete('end_date');
        }

        // Redirect with updated query parameters
        window.location.href = url.toString();


    }


    // Function to update the search result message
    function updateSearchResultMessage() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        const startDate = urlParams.get('start_date');
        const endDate = urlParams.get('end_date');

        let message = '';

        // If there is a search query, add it to the message
        if (searchQuery) {
            message += `Hasil pencarian untuk: "${searchQuery}"`;
            // Set the value of the search input
            document.getElementById('searchInputExport').value = searchQuery;
        } else {
            // Clear the search input if there's no query
            document.getElementById('searchInputExport').value = '';
        }

        // If there is a start date and end date, add them to the message
        if (startDate && endDate) {
            if (message) {
                message += ' dan '; // Connect search query with date range
            }
            message += `rentang tanggal: ${startDate} hingga ${endDate}`;

            // Set the values of the date inputs
            document.getElementById('datepicker-range-start-export').value = startDate;
            document.getElementById('datepicker-range-end-export').value = endDate;
        } else {
            // Clear the date inputs if there's no date range
            document.getElementById('datepicker-range-start-export').value = '';
            document.getElementById('datepicker-range-end-export').value = '';
        }

        // Update the message in the DOM
        if (message) {
            document.getElementById('searchResultMessage').textContent = message;
        } else {
            document.getElementById('searchResultMessage').textContent = '';
        }
    }
    // Pilih semua tombol dengan class 'detail-btn'
    const detailButtons = document.querySelectorAll('.detail-btn');

    // Tambahkan event listener pada setiap tombol
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Ambil id dari elemen baris <tr> yang merupakan parent dari tombol
            var rowId = this.closest('tr').id;
            console.log("ID dari baris yang diklik:", rowId);

            var modal = document.getElementById('static-modal');
            modal.classList.remove('hidden'); // Menampilkan modal dengan menghapus class 'hidden'

            // Mengambil data event dari server berdasarkan eventId
            fetch(`/siminbarpermintaanbarangadmin/${rowId}`) // Ganti URL sesuai dengan endpoint Anda
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Event not found');
                    }
                    return response.json();
                })
                .then(eventData => {
                    // Mengisi modal dengan data event
                    var modalTitle = modal.querySelector('h3');
                    modalTitle.textContent = eventData
                        .namabarang; // Mengganti judul modal dengan judul event
                    console.log(eventData.namabarang);
                    var modalId = modal.querySelector('#id');
                    modalId.textContent = eventData.id;

                    var modalDateStart = modal.querySelector(
                        '#status'); // Pastikan ada elemen dengan ID ini di modal
                    modalDateStart.textContent = eventData.status; // Mengganti teks tanggal mulai

                    var modalDateEnd = modal.querySelector(
                        '#user'); // Pastikan ada elemen dengan ID ini di modal
                    modalDateEnd.textContent = eventData.name; // Mengganti teks tanggal selesai

                    var modalTimeStart = modal.querySelector(
                        '#barang'); // Pastikan ada elemen dengan ID ini di modal
                    modalTimeStart.textContent = eventData
                        .namabarang; // Mengganti teks tanggal mulai

                    var modalStokPermintaan = modal.querySelector(
                        '#stokpermintaan'); // Pastikan ada elemen dengan ID ini di modal
                    modalStokPermintaan.textContent = eventData
                        .stokpermintaan; // Mengganti teks tanggal mulai

                    var modalOrderDate = modal.querySelector(
                        '#orderdate'); // Pastikan ada elemen dengan ID ini di modal
                    modalOrderDate.textContent = eventData
                        .orderdate; // Mengganti teks tanggal mulai

                    var modalCatatan = modal.querySelector(
                        '#catatan'); // Pastikan ada elemen dengan ID ini di modal
                    modalCatatan.textContent = eventData.catatan; // Mengganti teks tanggal mulai

                    var modalStokTersedia = modal.querySelector(
                        '#stoktersedia'); // Pastikan ada elemen dengan ID ini di modal
                    modalStokTersedia.textContent = eventData
                        .stoktersedia; // Mengganti teks tanggal mulai

                    // Mendapatkan elemen <img> untuk tanda tangan
                    var modalSignature = modal.querySelector('#imagettduser');
                    var signaturePath = eventData.ttduser;
                    // console.log(signaturePath);
                    // Mengubah src dari elemen <img> dengan path tanda tangan
                    modalSignature.setAttribute('src',
                        "{{ asset('storage/uploads/signatures') }}/" + signaturePath);


                    // Mendapatkan elemen <img> untuk tanda tangan admin
                    var modalSignature = modal.querySelector('#imagettdadmin');
                    var signatureContainer = modalSignature
                        .parentElement; // Mendapatkan elemen container yang membungkus <img>
                    var signaturePath = eventData.ttdadmin; // Pastikan eventData.ttdadmin benar
                    console.log(signaturePath);
                    // Memeriksa apakah signaturePath kosong
                    if (signaturePath && signaturePath.trim() !== "") {
                        // Jika signaturePath ada, ubah src dengan path tanda tangan
                        modalSignature.setAttribute('src',
                            "{{ asset('storage/uploads/signatures') }}/" + signaturePath);
                        modalSignature.style.display =
                            'block'; // Tampilkan gambar jika ada tanda tangan

                        // Hapus teks "-" jika sebelumnya sudah ada
                        var existingTextSignature = signatureContainer.querySelector(
                            '.text-signature');
                        if (existingTextSignature) {
                            existingTextSignature.remove();
                        }

                    } else {
                        // Jika signaturePath kosong, sembunyikan gambar dan tampilkan tanda "-"
                        modalSignature.style.display = 'none'; // Sembunyikan gambar

                        // Periksa apakah teks "-" sudah ada
                        if (!signatureContainer.querySelector('.text-signature')) {
                            var textSignature = document.createElement('span');
                            textSignature.classList.add(
                                'text-signature'); // Beri class untuk memudahkan styling
                            textSignature.textContent = '-'; // Isi dengan tanda "-"
                            signatureContainer.appendChild(
                                textSignature); // Tambahkan teks setelah elemen <img>
                        }
                    }

                    // Mendapatkan elemen <img> untuk tanda tangan admin
                    var modalSignature = modal.querySelector('#imagettdumum');
                    var signatureContainer = modalSignature
                        .parentElement; // Mendapatkan elemen container yang membungkus <img>
                    var signaturePathUmum = eventData.ttdumum; // Pastikan eventData.ttdadmin benar

                    // Memeriksa apakah signaturePath kosong
                    if (signaturePathUmum && signaturePathUmum.trim() !== "") {
                        // Jika signaturePath ada, ubah src dengan path tanda tangan
                        modalSignature.setAttribute('src',
                            "{{ asset('storage/uploads/signatures') }}/" + signaturePathUmum);
                        modalSignature.style.display =
                            'block'; // Tampilkan gambar jika ada tanda tangan

                        // Hapus teks "-" jika sebelumnya sudah ada
                        var existingTextSignature = signatureContainer.querySelector(
                            '.text-signature');
                        if (existingTextSignature) {
                            existingTextSignature.remove();
                        }

                    } else {
                        // Jika signaturePath kosong, sembunyikan gambar dan tampilkan tanda "-"
                        modalSignature.style.display = 'none'; // Sembunyikan gambar

                        // Periksa apakah teks "-" sudah ada
                        if (!signatureContainer.querySelector('.text-signature')) {
                            var textSignature = document.createElement('span');
                            textSignature.classList.add(
                                'text-signature'); // Beri class untuk memudahkan styling
                            textSignature.textContent = '-'; // Isi dengan tanda "-"
                            signatureContainer.appendChild(
                                textSignature); // Tambahkan teks setelah elemen <img>
                        }
                    }




                    // Mendapatkan elemen <img> untuk tanda tangan admin
                    var modalSignature = modal.querySelector('#imagebukti');
                    var signatureContainer = modalSignature
                        .parentElement; // Mendapatkan elemen container yang membungkus <img>
                    var signaturePath = eventData
                        .buktifoto; // Pastikan eventData.ttdadmin benar

                    // Memeriksa apakah signaturePath kosong
                    if (signaturePath && signaturePath.trim() !== "") {
                        // Jika signaturePath ada, ubah src dengan path tanda tangan
                        modalSignature.setAttribute('src',
                            "{{ asset('storage/uploads/images') }}/" + signaturePath);
                        modalSignature.style.display =
                            'block'; // Tampilkan gambar jika ada tanda tangan

                        // Hapus teks "-" jika sebelumnya sudah ada
                        var existingTextSignature = signatureContainer.querySelector(
                            '.text-signature');
                        if (existingTextSignature) {
                            existingTextSignature.remove();
                        }

                    } else {
                        // Jika signaturePath kosong, sembunyikan gambar dan tampilkan tanda "-"
                        modalSignature.style.display = 'none'; // Sembunyikan gambar

                        // Periksa apakah teks "-" sudah ada
                        if (!signatureContainer.querySelector('.text-signature')) {
                            var textSignature = document.createElement('span');
                            textSignature.classList.add(
                                'text-signature'); // Beri class untuk memudahkan styling
                            textSignature.textContent = '-'; // Isi dengan tanda "-"
                            signatureContainer.appendChild(
                                textSignature); // Tambahkan teks setelah elemen <img>
                        }
                    }


                    var modalStatus = modal.querySelector('#status');
                    modalStatus.textContent = eventData.status; // Menyimpan status

                    // Menampilkan atau menyembunyikan tombol berdasarkan status
                    var editButton = modal.querySelector('#edit-button');
                    var umumButton = modal.querySelector('#umum-button');
                    if (modalStatus.textContent === 'disapproved') {
                        editButton.classList.remove('hidden');
                        // umumButton.classList.add('hidden');
                    } else {
                        // Memeriksa apakah signaturePath kosong
                        umumButton.classList.remove('hidden');
                        if (signaturePathUmum == null) {
                            umumButton.classList.remove('hidden');

                        } else {
                            umumButton.classList.add('hidden');
                            // editButton.classList.add('hidden');
                        }
                    }

                })
                .catch(error => {
                    console.error('Error fetching event data:', error);
                    // Tampilkan pesan kesalahan jika diperlukan
                });



            // Tambahkan event listener untuk menutup modal
            modal.querySelectorAll('[data-modal-hide]').forEach(function(button) {
                button.addEventListener('click', function() {
                    modal.classList.add(
                        'hidden'
                    ); // Menyembunyikan modal dengan menambahkan class 'hidden'
                    location.reload();
                });
            });
        });
    });
</script>

</html>
