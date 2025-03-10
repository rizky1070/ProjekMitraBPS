<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
    class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>
<div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
    class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
    <div class=" flex items-center justify-center mt-8">
        <a href="/dashboard">
            <div class="flex items-center">
                <img src="logo aja bps.png" class="w-12 h-12" alt="Logo BPS">

                <span class="mx-2 text-2xl font-semibold text-white">KANAL 3516</span>
            </div>
        </a>
    </div>

    <nav class="mt-10">



            <!-- User -->
            <div>
                <!-- admin -->
          
            
        @php
            // Cek apakah ada link yang aktif di dalam dropdown
            $isDropdownAgenkitaActive =
                request()->is('agenkitaagenda') ||
                request()->is('agenkitapresensi') ||
                request()->is('agenkitanotulen');
        @endphp

        <div>
            <!-- Button Dropdown -->
            <div class="flex items-center">
                <img src="agen kita.png" alt="Logo agenkita" class="ml-4 w-12 h-12" />
                <button
                    class="dropdown-btn flex items-center px-4 py-3 text-lg {{ $isDropdownAgenkitaActive ? 'text-white' : 'text-gray-500' }} hover:bg-opacity-80 hover:text-white transition-all duration-300 rounded-md">
                    AGENKITA
                    <i class="fa fa-caret-down ml-2"></i>
                </button>
            </div>


            <!-- Dropdown Container -->
            <div class="dropdown-container {{ $isDropdownAgenkitaActive ? 'block' : 'hidden' }}">
                <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('agenkitaagenda') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                    href="/agenkitaagenda">

                    <span class="mx-3">Agenda</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('agenkitapresensi') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                    href="/agenkitapresensi">

                    <span class="mx-3">Presensi</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('agenkitanotulen') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                    href="/agenkitanotulen">

                    <span class="mx-3">Notulen</span>
                </a>
            </div>
        </div>


        @php
            // Cek apakah ada link yang aktif di dalam dropdown
            $isDropdownHamuktiActive =
                request()->is('hamuktisuratkeluar') ||
                request()->is('hamuktisuratmasuk') ||
                request()->is('hamuktisk') ||
                request()->is('hamuktisurattugas') ||
                request()->is('hamuktikontrak') ||
                request()->is('hamuktiba');
        @endphp

        @if (auth()->user()->is_admin || auth()->user()->is_hamukti)
            <div>
                <!-- Button Dropdown -->
                <div class="flex items-center">
                    <img src="hamukti.png" alt="Logo hamukti" class="ml-4 w-12 h-12" />
                    <button
                        class="dropdown-btn flex items-center px-4 py-3 text-lg {{ $isDropdownHamuktiActive ? 'text-white' : 'text-gray-500' }} hover:bg-opacity-80 hover:text-white transition-all duration-300 rounded-md">
                        HAMUKTI
                        <i class="fa fa-caret-down ml-2"></i>
                    </button>
                </div>

                <!-- Dropdown Container -->
                <div class="dropdown-container {{ $isDropdownHamuktiActive ? 'block' : 'hidden' }}">
                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('hamuktisuratkeluar') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/hamuktisuratkeluar">

                        <span class="mx-3">Surat Keluar</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('hamuktisuratmasuk') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/hamuktisuratmasuk">

                        <span class="mx-3">Surat Masuk</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('hamuktisk') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/hamuktisk">

                        <span class="mx-3">SK</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('hamuktisurattugas') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/hamuktisurattugas">

                        <span class="mx-3">Surat Tugas</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('hamuktikontrak') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/hamuktikontrak">

                        <span class="mx-3">Kontrak</span>
                    </a>

                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('hamuktiba') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/hamuktiba">

                        <span class="mx-3">BA</span>
                    </a>
                </div>
            </div>
        @endif
            
            
                
        @php
                // Cek apakah ada link yang aktif di dalam dropdown
                $isDropdownPrismaActive =
                    request()->is('izinkeluar') ||
                    request()->is('izinkeluarhistori') ||
                    request()->is('izinkeluarform');
        @endphp
        <div>
    <!-- Button Dropdown -->
    <div class="flex items-center">
        <img src="prisma.png" alt="Logo agenkita" class="ml-4 w-12 h-12" />
        <button
            class="dropdown-btn flex items-center px-4 py-3 text-lg {{ $isDropdownPrismaActive ? 'text-white' : 'text-gray-500' }} hover:bg-opacity-80 hover:text-white transition-all duration-300 rounded-md">
            PRISMA
            <i class="fa fa-caret-down ml-2"></i>
        </button>
    </div>

    <div class="dropdown-container {{ $isDropdownPrismaActive ? 'block' : 'hidden' }}">
        @if (auth()->user()->is_admin)
            <!-- Admin - Daftar Izin dan Histori Izin -->
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('izinkeluar') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
               href="/izinkeluar">
                <span class="mx-3">Daftar Izin</span>
            </a>
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('izinkeluarhistori') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
               href="/izinkeluarhistori">
                <span class="mx-3">Histori Izin</span>
            </a>
        @elseif (auth()->user()->is_leader)
            <!-- Leader - Daftar Izin, Izin Keluar, dan Histori Izin -->
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('izinkeluar') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
               href="/izinkeluar">
                <span class="mx-3">Daftar Izin</span>
            </a>
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('izinkeluarform') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
               href="/izinkeluarform">
                <span class="mx-3">Izin Keluar</span>
            </a>
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('izinkeluarhistori') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
               href="/izinkeluarhistori">
                <span class="mx-3">Histori Izin</span>
            </a>
        @else
            <!-- Bukan Admin atau Leader - hanya Izin Keluar -->
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('izinkeluarform') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
               href="/izinkeluarform">
                <span class="mx-3">Izin Keluar</span>
            </a>
        @endif
    </div>
</div>



            <a class="flex items-center px-6 py-2  text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100"
                href="https://www.link3516.com">
                <img src="supertim.png" alt="Logo Super Tim" class="w-12 h-12" />


                <span class="mx-3">SETAPE </span>
            </a>

        </div>


        
        @php
            // Cek apakah ada link yang aktif di dalam dropdown
            $isDropdownSiminbarActive =
                request()->is('siminbardaftarbarang') ||
                request()->is('siminbarpermintaanbarangadmin') ||
                request()->is('siminbarpermintaanbarang');
        @endphp
        <div>
            <!-- Button Dropdown -->
            <div class="flex items-center">
                <img src="siminbar.png" alt="Logo siminbar" class="ml-4 w-12 h-12" />
                <button
                    class="dropdown-btn flex items-center px-4 py-3 text-lg {{ $isDropdownSiminbarActive ? 'text-white' : 'text-gray-500' }} hover:bg-opacity-80 hover:text-white transition-all duration-300 rounded-md">
                    SIMINBAR
                    <i class="fa fa-caret-down ml-2"></i>
                </button>
            </div>

            <!-- Dropdown Container -->
            <div class="dropdown-container {{ $isDropdownSiminbarActive ? 'block' : 'hidden' }}">
               <!-- Link: Daftar Barang (Cek Stok) - Hanya untuk Admin -->
    @if (auth()->check() && auth()->user()->jabatan == 'admin')
        <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('siminbardaftarbarang') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
            href="/siminbardaftarbarang">
            <span class="mx-3">Cek Stok</span>
        </a>
    @endif

                <!-- Link: Permintaan Barang (Admin/Kabag Umum) -->
                @if (auth()->user()->jabatan == 'admin' || auth()->user()->jabatan == 'Kasubag Umum')
                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('siminbarpermintaanbarangadmin') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/siminbarpermintaanbarangadmin">

                        <span class="mx-3">Permintaan Barang</span>
                    </a>
                @else
                    <!-- Link: Permintaan Barang (User) -->
                    <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('siminbarpermintaanbarang') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                        href="/siminbarpermintaanbarang">

                        <span class="mx-3">Permintaan Barang</span>
                    </a>
                @endif
            </div>
        </div>

<div>
<!-- Button Dropdown -->
    <div class="flex items-center">
        <img src="mitrabps.png" alt="Logo mitabps" class="ml-4 w-12 h-12" />
        <button
            class="dropdown-btn flex items-center px-4 py-3 text-lg {{ $isDropdownPrismaActive ? 'text-white' : 'text-gray-500' }} hover:bg-opacity-80 hover:text-white transition-all duration-300 rounded-md">
            MITRA BPS
            <i class="fa fa-caret-down ml-2"></i>
        </button>
    </div>

    <div class="dropdown-container {{ $isDropdownPrismaActive ? 'block' : 'hidden' }}">
        @if (auth()->user()->is_admin)
            <!-- Admin - Daftar Izin dan Histori Izin -->
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('/inputmitrabps') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                href="/daftarsurveibps">
                <span class="mx-3">Mitra BPS</span>
            </a>
            <a class="flex items-center px-6 py-2 mt-4 ml-10 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 {{ request()->is('izinkeluarhistori') ? 'text-white bg-gray-700 bg-opacity-50' : '' }}"
                href="/izinkeluarhistori">
                <span class="mx-3">Survei</span>
            </a>
        @endif
    </div>
</div>



    </nav>
</div>
<script>
    // var dropdown = document.getElementsByClassName("dropdown-btn");
    // var i;

    // for (i = 0; i < dropdown.length; i++) {
    //     dropdown[i].addEventListener("click", function() {
    //         this.classList.toggle("active");
    //         var dropdownContent = this.nextElementSibling;
    //         if (dropdownContent.style.display === "block") {
    //             dropdownContent.style.display = "none";
    //         } else {
    //             dropdownContent.style.display = "block";
    //         }
    //     });
    // }
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil semua elemen tombol dropdown dan kontainer dropdown
        var dropdownBtns = document.querySelectorAll('.dropdown-btn');
        var dropdownContainers = document.querySelectorAll('.dropdown-container');

        // Loop untuk menambahkan event listener pada setiap tombol dropdown
        dropdownBtns.forEach(function(dropdownBtn, index) {
            // Menentukan kontainer dropdown yang sesuai berdasarkan indeks
            var dropdownContainer = dropdownContainers[index];

            // Tambahkan event listener pada tombol dropdown
            dropdownBtn.addEventListener('click', function() {
                // Toggle kelas 'hidden' untuk menampilkan atau menyembunyikan dropdown
                dropdownContainer.classList.toggle('hidden');

                // Optionally, toggle aktif class pada tombol untuk efek visual
                dropdownBtn.classList.toggle('active');
            });
        });
    });
</script>
