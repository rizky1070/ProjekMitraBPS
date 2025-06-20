<?php
$title = 'Kelola Survei';
?>
@include('mitrabps.headerTemp')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<style>
    /* Style untuk modal konfirmasi universal */
    .confirmation-modal {
        display: none;
        /* Disembunyikan secara default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .confirmation-modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        text-align: left;
    }

    .error-list {
        list-style-type: disc;
        margin-left: 20px;
        color: #EF4444;
        /* red-500 */
    }

    #dropdownPortal {
        top: 0;
        left: 0;
        width: 100%;
        pointer-events: none; /* Biarkan interaksi tetap ke select */
    }

    .ts-dropdown {
        position: absolute !important;
        z-index: 10000 !important;
        max-height: 300px !important;
        overflow-y: auto !important;
        pointer-events: auto; /* Aktifkan interaksi */
    }
</style>
@include('mitrabps.cuScroll')
</head>

<body class="cuScrollGlobalY h-full bg-gray-200 mb-4">
    {{-- Pesan Sukses/Error Global (dari SweetAlert) --}}
    @if (session('success'))
        <script>
            swal("Success!", "{{ session('success') }}", "success");
        </script>
    @endif

    @if (session('error'))
        <script>
            swal("Error!", "{!! session('error') !!}", "error");
        </script>
    @endif

    {{-- HAPUS blok @if (session('confirm')) yang lama. Kita ganti dengan modal universal di bawah --}}

    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-modal-content">
            <h3 class="text-lg font-bold mb-4" id="modalTitle">Konfirmasi Aksi</h3>
            <div id="modalBody">
                <p id="modalMessage" class="mb-4">Apakah Anda yakin ingin melanjutkan?</p>
                {{-- Area untuk menampilkan error validasi --}}
                <div id="modalErrors" class="mb-4 hidden">
                    <p class="font-bold text-red-600">Harap perbaiki error berikut:</p>
                    <ul id="modalErrorList" class="error-list"></ul>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button id="cancelButton" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Batal
                </button>
                <button id="confirmButton" class="px-4 py-2 bg-oren text-white rounded hover:bg-orange-500">
                    Iya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
    <main class="flex-1 overflow-x-hidden bg-gray-200">

        <a href="{{ url('/daftarSurvei') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-oren hover:bg-orange-500 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Detail Survei</h2>
                {{-- Form Hapus Survei --}}
                @if (auth()->user()->is_admin || auth()->user()->is_leader)
                <form action="{{ route('survey.delete', ['id_survei' => $survey->id_survei]) }}" method="POST"
                    id="form-delete-survey">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="showConfirmation('hapus_survei', 0, '{{ $survey->nama_survei }}')"
                        class="flex justify-center items-center mt-4 px-4 py-2 bg-red-500 text-white font-medium rounded-md hover:bg-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>Hapus Survei
                    </button>
                </form>
                @endif
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xl font-medium"><strong>Nama Survei :</strong> {{ $survey->nama_survei }}</p>
                <div class="flex flex-col md:flex-row items-start md:items-center w-full">
                    <div class="w-full md:w-1/2">
                        <p><strong>Pelaksanaan :</strong>
                            {{ \Carbon\Carbon::parse($survey->jadwal_kegiatan)->translatedFormat('j F Y') }} -
                            {{ \Carbon\Carbon::parse($survey->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}
                        </p>
                        <p><strong>Tim :</strong> {{ $survey->tim }}</p>
                    </div>
                    <div class="w-full md:w-1/2">
                        <p><strong>KRO :</strong> {{ $survey->kro }} </p>
                        <p><strong>Jumlah mitra :</strong> {{ $survey->mitra_survei_count }} </p>
                    </div>
                </div>

                <div class="flex items-center">
                    <p><strong>Status :</strong>
                        <span class="font-bold">
                            @if ($survey->status_survei == 1)
                                <div class="bg-red-500 text-white  px-2 py-1 rounded ml-2 mr-5">Belum Dikerjakan</div>
                            @elseif($survey->status_survei == 2)
                                <div class="bg-yellow-300 text-white  px-2 py-1 rounded ml-2 mr-5">Sedang Dikerjakan
                                </div>
                            @elseif($survey->status_survei == 3)
                                <div class="bg-green-500 text-white  px-2 py-1 rounded ml-2 mr-5">Sudah Dikerjakan</div>
                            @else
                                <span class="bg-gray-500 text-white rounded-md px-2 py-1 ml-2">Status Tidak
                                    Diketahui</span>
                            @endif
                        </span>
                    </p>
                </div>

                <script>
                    function toggleDropdown() {
                        var dropdown = document.getElementById("dropdown");
                        dropdown.classList.toggle("hidden");
                    }
                </script>

            </div>
            <div class="flex flex-col md:flex-row justify-between mb-4">
                <h3 class="text-xl font-bold mt-4">Daftar Mitra</h3>
                <div>
                    <div class="flex gap-2">
                        {{-- Form Hapus Semua Mitra --}}
                        @if ($survey->mitra_survei_count > 0)
                            <a href="{{ route('mitraSurvei.export.excel', ['id_survei' => $survey->id_survei]) }}"
                                class="flex justify-center items-center mt-4 px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-all duration-300">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </a>
                            @if (auth()->user()->is_admin || auth()->user()->is_leader)
                            <form action="{{ route('survey.deleteAllMitra', ['id_survei' => $survey->id_survei]) }}"
                                method="POST" id="form-hapus_semua_mitra-{{ $survey->id_survei }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="showConfirmation('hapus_semua_mitra', {{ $survey->id_survei }}, '{{ $survey->nama_survei }}')"
                                    class="flex justify-center items-center mt-4 px-4 py-2 bg-red-800 text-white font-medium rounded-md hover:bg-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>Hapus Semua Mitra
                                </button>
                            </form>
                            @endif
                        @endif
                        @if (auth()->user()->is_admin || auth()->user()->is_leader)
                        <button type="button"
                            class="mt-4 px-4 py-2 bg-oren rounded-md text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300"
                            onclick="openModal()">+ Tambah
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="cuScrollFilter bg-white rounded-lg shadow-sm p-6">
                <!-- Year Row -->
                <form id="filterForm" action="{{ route('editSurvei.filter', ['id_survei' => $survey->id_survei]) }}"
                    method="GET" class="space-y-4">
                    <!-- Survey Name Row -->
                    <div class="flex items-center relative">
                        <label for="nama_lengkap" class="w-32 text-lg font-semibold text-gray-800 mb-4">Cari
                            Mitra</label>
                        <select name="nama_lengkap" id="nama_mitra"
                            class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                            {{ empty($namaMitraOptions) ? 'disabled' : '' }}>
                            <option value="">Semua Mitra</option>
                            @foreach ($namaMitraOptions as $nama => $label)
                                <option value="{{ $nama }}" @if (request('nama_lengkap') == $nama) selected @endif>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Filter Mitra</h2>
                    </div>
                    <div class="flex">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                            <div class="flex items-center">
                                <label for="tahun" class="w-32 text-sm font-medium text-gray-700">Tahun</label>
                                <select name="tahun" id="tahun"
                                    class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($tahunOptions as $year => $yearLabel)
                                        <option value="{{ $year }}"
                                            @if (request('tahun') == $year) selected @endif>{{ $yearLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Month Row -->
                            <div class="flex items-center">
                                <label for="bulan" class="w-32 text-sm font-medium text-gray-700">Bulan</label>
                                <select name="bulan" id="bulan"
                                    class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                                    {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                    <option value="">Semua Bulan</option>
                                    @foreach ($bulanOptions as $month => $monthName)
                                        <option value="{{ $month }}"
                                            @if (request('bulan') == $month) selected @endif>{{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- District Row -->
                            <div class="flex items-center">
                                <label for="kecamatan"
                                    class="w-32 text-sm font-medium text-gray-700">Kecamatan</label>
                                <select name="kecamatan" id="kecamatan"
                                    class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                                    {{ empty($kecamatanOptions) ? 'disabled' : '' }}>
                                    <option value="">Semua Kecamatan</option>
                                    @foreach ($kecamatanOptions as $kecam)
                                        <option value="{{ $kecam->id_kecamatan }}"
                                            @if (request('kecamatan') == $kecam->id_kecamatan) selected @endif>
                                            [{{ $kecam->kode_kecamatan }}] {{ $kecam->nama_kecamatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                @if (session('survei_warnings'))
                    <div class="mt-2 mb-4 p-3 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
                        <h4 class="font-bold">Peringatan Survei:</h4>
                        <ul class="list-disc pl-5">
                            @foreach (session('survei_warnings') as $warning)
                                <li class="text-sm">{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('import_errors'))
                    <div class="mt-2 mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <h4 class="font-bold">Mitra yang gagal diimport ke survei:</h4>
                        <ul class="cuScrollError list-disc pl-5">
                            @foreach (session('import_errors') as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('honor_warnings'))
                    <div class="mt-2 mb-4 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                        <h4 class="font-bold">Peringatan Honor Mitra:</h4>
                        <ul class="list-disc pl-5">
                            @foreach (session('honor_warnings') as $warning)
                                <li class="text-sm">{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <!-- JavaScript Tom Select -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <!-- Inisialisasi Tom Select -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect('#nama_mitra', {
                    placeholder: 'Pilih Mitra',
                    searchField: 'text',
                });

                new TomSelect('#tahun', {
                    placeholder: 'Pilih Tahun',
                    searchField: 'text',
                });

                new TomSelect('#bulan', {
                    placeholder: 'Pilih Bulan',
                    searchField: 'text',
                });

                new TomSelect('#kecamatan', {
                    placeholder: 'Pilih Kecamatan',
                    searchField: 'text',
                });

                // Initialize Tom Select for position dropdowns
                document.querySelectorAll('select[name="id_posisi_mitra"]').forEach(select => {
                    new TomSelect(select, {
                        placeholder: 'Pilih Posisi',
                        searchField: 'text',
                        onChange: function(value) {
                            const mitraId = select.getAttribute('onchange').match(/\d+/)[0];
                            updateRateHonor(select, mitraId);
                        }
                    });
                });

                // Auto submit saat filter berubah
                const filterForm = document.getElementById('filterForm');
                const tahunSelect = document.getElementById('tahun');
                const bulanSelect = document.getElementById('bulan');
                const kecamatanSelect = document.getElementById('kecamatan');
                const mitraSelect = document.getElementById('nama_mitra');

                // Ganti fungsi submitForm dengan ini
                let timeout;

                function submitForm() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        filterForm.submit();
                    }, 500); // Delay 500ms sebelum submit
                }

                // Tambahkan event listener untuk setiap select
                tahunSelect.addEventListener('change', submitForm);
                bulanSelect.addEventListener('change', submitForm);
                kecamatanSelect.addEventListener('change', submitForm);
                mitraSelect.addEventListener('change', submitForm);

                const portal = document.getElementById('dropdownPortal');

  // Inisialisasi TomSelect untuk posisi mitra
                document.querySelectorAll('select[name="id_posisi_mitra"]').forEach(select => {
                    const tomSelect = new TomSelect(select, {
                    placeholder: 'Pilih Posisi',
                    onDropdownOpen: () => {
                        // Pindahkan dropdown ke portal saat terbuka
                        const dropdown = select.tomselect.dropdown;
                        portal.appendChild(dropdown);
                        
                        // Atur posisi dropdown relatif terhadap select asli
                        const selectRect = select.getBoundingClientRect();
                        dropdown.style.top = `${selectRect.bottom + window.scrollY}px`;
                        dropdown.style.left = `${selectRect.left + window.scrollX}px`;
                        dropdown.style.width = `${selectRect.width}px`;
                    },
                    onDropdownClose: () => {
                        // Kembalikan dropdown ke DOM asli (opsional)
                        if (select.tomselect) {
                        select.tomselect.wrapper.appendChild(select.tomselect.dropdown);
                        }
                    }
                    });
                });

                // Update posisi dropdown saat scroll/resize
                window.addEventListener('scroll', updateDropdownPosition);
                window.addEventListener('resize', updateDropdownPosition);

                function updateDropdownPosition() {
                    document.querySelectorAll('select[name="id_posisi_mitra"]').forEach(select => {
                    if (select.tomselect && select.tomselect.isOpen) {
                        const dropdown = select.tomselect.dropdown;
                        const selectRect = select.getBoundingClientRect();
                        dropdown.style.top = `${selectRect.bottom + window.scrollY}px`;
                        dropdown.style.left = `${selectRect.left + window.scrollX}px`;
                    }
                    });
                }
            });
        </script>
        <div class="border rounded-lg shadow-sm bg-white bg-white p-2 mx-4">
            <div class="cuScrollTableX">
                <table class="min-w-full divide-y divide-gray-500">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Mitra</th>
                            <th scope="col"
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kecamatan</th>
                            <th scope="col"
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Survei yang Diikuti</th>
                            <th scope="col"
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mitra Tahun</th>
                            <th scope="col"
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vol</th>
                            <th scope="col"
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rate Honor</th>
                            <th scope="col"
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Posisi</th>
                            @if (auth()->user()->is_admin || auth()->user()->is_leader)
                            <th scope="col"
                                class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-500">
                        {{-- BLOK PHP YANG DISEMPURNAKAN: Cek error dan konfirmasi --}}
                        @php
                            $errorMitraId = null;
                            if (session('show_modal')) {
                                $parts = explode('-', session('show_modal'));
                                if (count($parts) === 2) {
                                    $errorMitraId = (int) $parts[1];
                                }
                            } elseif (session('show_modal_confirmation')) {
                                // Ini memastikan data lama (old) tetap terisi bahkan saat pop up konfirmasi honor muncul
                                $confirmData = session('show_modal_confirmation');
                                $errorMitraId = $confirmData['mitra_id'];
                            }
                        @endphp

                        @foreach ($mitras as $mitra)
                            <tr class="hover:bg-gray-50" style="border-top-width: 2px; border-color: #D1D5DB;">
                                {{-- Kolom info mitra (tidak berubah) --}}
                                <td class="whitespace-normal text-center break-words" style="max-width: 120px;">
                                    <div class="flex text-left ml-2">
                                        <a href="/profilMitra/{{ $mitra->id_mitra }}"
                                            class="hover:underline transition duration-300 ease-in-out"
                                            style="text-decoration-color: #FFA500; text-decoration-thickness: 3px;">
                                            {{ $mitra->nama_lengkap }}
                                        </a>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap text-center" style="max-width: 120px;">
                                    {{ $mitra->kecamatan->nama_kecamatan ?? 'Lokasi tidak tersedia' }}
                                </td>
                                <td class="whitespace-nowrap text-center" style="max-width: 100px;">
                                    {{ $mitra->total_survei }}
                                </td>
                                <td class="text-center whitespace-normal break-words" style="max-width: 120px;">
                                    {{ \Carbon\Carbon::parse($mitra->tahun)->translatedFormat('Y') }}
                                </td>
                            @if (auth()->user()->is_admin || auth()->user()->is_leader)
                                @if ($mitra->isFollowingSurvey)
                                    {{-- Kolom input Vol --}}
                                    <td class="whitespace-nowrap text-center" style="max-width: 120px;">
                                        <input type="number" name="vol"
                                            value="{{ $errorMitraId == $mitra->id_mitra ? old('vol') : $mitra->vol }}"
                                            class="w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="Vol" form="form-edit-{{ $mitra->id_mitra }}">
                                        {{-- Tautkan ke form edit --}}
                                    </td>

                                    {{-- Kolom input Rate Honor --}}
                                    <td class="whitespace-nowrap text-center" style="max-width: 120px;">
                                        <input type="number" name="rate_honor"
                                            value="{{ $errorMitraId == $mitra->id_mitra ? old('rate_honor') : $mitra->rate_honor }}"
                                            class="w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="Rate Honor" form="form-edit-{{ $mitra->id_mitra }}">
                                        {{-- Tautkan ke form edit --}}
                                    </td>

                                    {{-- Kolom select Posisi --}}
                                    <td class="text-center" style="max-width: 120px;">
                                        <select name="id_posisi_mitra" class="w-full focus:outline-none text-left"
                                            form="form-edit-{{ $mitra->id_mitra }}"> {{-- Tautkan ke form edit --}}
                                            <option value="">Pilih Posisi</option>
                                            @foreach ($posisiMitraOptions as $posisi)
                                                <option value="{{ $posisi->id_posisi_mitra }}"
                                                    @if (($errorMitraId == $mitra->id_mitra ? old('id_posisi_mitra') : $mitra->id_posisi_mitra) == $posisi->id_posisi_mitra) selected @endif>
                                                    {{ $posisi->nama_posisi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Kolom Aksi (Tombol & Form diletakkan di sini) --}}
                                    <td class="whitespace-nowrap text-center" style="max-width: 120px;">
                                        <div class="flex justify-center items-center py-2 text-center">
                                            {{-- FORM UNTUK EDIT sekarang berada di dalam <td> --}}
                                            <form
                                                action="{{ route('mitra.update', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}"
                                                method="POST" id="form-edit-{{ $mitra->id_mitra }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="force_action" value="1"
                                                    class="force-action-input" disabled>
                                                <button type="button"
                                                    onclick="showConfirmation('edit', {{ $mitra->id_mitra }}, '{{ $mitra->nama_lengkap }}')"
                                                    class="bg-oren text-white px-2 py-1 rounded hover:bg-orange-500 mr-3"
                                                    title="Simpan">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </button>
                                            </form>

                                            {{-- FORM UNTUK HAPUS sekarang berada di dalam <td> --}}
                                            <form
                                                action="{{ route('mitra.delete', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}"
                                                method="POST" id="form-hapus-{{ $mitra->id_mitra }}"
                                                class="inline">
                                                @csrf
                                                <button type="button"
                                                    onclick="showConfirmation('hapus', {{ $mitra->id_mitra }}, '{{ $mitra->nama_lengkap }}')"
                                                    class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"
                                                    title="Hapus">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @else
                                    {{-- Kolom input Vol --}}
                                    <td class="whitespace-nowrap text-center" style="max-width: 120px;">
                                        <input type="number" name="vol"
                                            value="{{ $errorMitraId == $mitra->id_mitra ? old('vol') : '' }}"
                                            class="w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="Masukkan Vol" form="form-tambah-{{ $mitra->id_mitra }}">
                                        {{-- Tautkan ke form tambah --}}
                                    </td>

                                    {{-- Kolom input Rate Honor --}}
                                    <td class="whitespace-nowrap text-center" style="max-width: 100px;">
                                        <input type="number" name="rate_honor"
                                            value="{{ $errorMitraId == $mitra->id_mitra ? old('rate_honor') : '' }}"
                                            class="w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="Rate Honor" form="form-tambah-{{ $mitra->id_mitra }}">
                                        {{-- Tautkan ke form tambah --}}
                                    </td>

                                    {{-- Kolom select Posisi --}}
                                    <td class=" text-center" style="max-width: 120px;">
                                        <select name="id_posisi_mitra" class="w-full focus:outline-none text-left"
                                            form="form-tambah-{{ $mitra->id_mitra }}"> {{-- Tautkan ke form tambah --}}
                                            <option value="">Pilih Posisi</option>
                                            @foreach ($posisiMitraOptions as $posisi)
                                                <option value="{{ $posisi->id_posisi_mitra }}"
                                                    @if ($errorMitraId == $mitra->id_mitra && old('id_posisi_mitra') == $posisi->id_posisi_mitra) selected @endif>
                                                    {{ $posisi->nama_posisi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Kolom Aksi (Tombol & Form diletakkan di sini) --}}
                                    <td class="whitespace-nowrap p-2 text-center" style="max-width: 120px;"
                                        colspan="2">
                                        {{-- FORM UNTUK TAMBAH sekarang ada di dalam <td>. Ini HTML yang valid. --}}
                                        <form
                                            action="{{ route('mitra.toggle', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}"
                                            method="POST" id="form-tambah-{{ $mitra->id_mitra }}">
                                            @csrf
                                            <input type="hidden" name="force_action" value="1"
                                                class="force-action-input" disabled>
                                        </form>

                                        {{-- Tombol ini akan men-trigger form di atas melalui JavaScript --}}
                                        <button type="button"
                                            onclick="showConfirmation('tambah', {{ $mitra->id_mitra }}, '{{ $mitra->nama_lengkap }}')"
                                            class="bg-green-500 px-3 rounded text-white font-medium hover:bg-green-600 hover:shadow-lg transition-all duration-300">
                                            Tambah
                                        </button>
                                    </td>
                                @endif
                            @else
                                <td class="whitespace-nowrap text-center" style="max-width: 120px;">
                                    {{ $mitra->vol ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap text-center" style="max-width: 100px;">
                                    {{ $mitra->rate_honor ?? '-' }}
                                </td>
                                <td class="text-center whitespace-normal break-words" style="max-width: 120px;">
                                    {{ $mitra->nama_posisi ?? '-' }}
                                </td>
                            @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
<div id="dropdownPortal" class="fixed z-[9999]"></div>
            </div>
        </div>
        </div>
        @include('components.pagination', ['paginator' => $mitras])
        </div>
    </main>

    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden"
        style="z-index: 50;">
        <div
            class="bg-white p-4 sm:p-6 rounded-lg shadow-lg w-11/12 sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/3 mx-2 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg sm:text-xl font-bold mb-2">Import Mitra ke Survei</h2>
            <p class="mb-2 text-red-700 text-xs sm:text-sm font-bold">Pastikan format file excel yang diimport sesuai!
            </p>

            <!-- Error/Success Messages -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 text-xs sm:text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-xs sm:text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('upload.excel', ['id_survei' => $survey->id_survei]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls"
                    class="border p-2 w-full text-xs sm:text-sm mb-2">
                <p class="py-2 text-xs sm:text-sm">Belum punya file excel?
                    <a href="{{ asset('addMitra2Survey.xlsx') }}" class="text-blue-500 hover:text-blue-600 font-bold"
                        download>
                        Download template disini.
                    </a>
                </p>
                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button"
                        class="px-3 py-1 sm:px-4 sm:py-2 bg-gray-500 text-white rounded-md text-xs sm:text-sm font-medium hover:bg-gray-600 transition-all duration-300"
                        onclick="closeModal()">Batal</button>
                    <button type="submit"
                        class="px-3 py-1 sm:px-4 sm:py-2 bg-oren rounded-md text-white text-xs sm:text-sm font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300">Unggah</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }
    </script>

    <script>
        // Ambil elemen-elemen modal
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalErrors = document.getElementById('modalErrors');
        const modalErrorList = document.getElementById('modalErrorList');
        const confirmButton = document.getElementById('confirmButton');
        const cancelButton = document.getElementById('cancelButton');

        // Fungsi untuk menampilkan modal konfirmasi
        function showConfirmation(action, id, name, force = false, customMessage = '', errors = null) {
            let title = '';
            let message = '';
            let formId = `form-${action}-${id}`;

            // Atur pesan default berdasarkan aksi
            switch (action) {
                case 'tambah':
                    title = `Konfirmasi Tambah Mitra`;
                    message = `Anda yakin ingin menambahkan <b>${name}</b> ke survei ini?`;
                    break;
                case 'edit':
                    title = `Konfirmasi Simpan Perubahan`;
                    message = `Anda yakin ingin menyimpan perubahan untuk mitra <b>${name}</b>?`;
                    break;
                case 'hapus':
                    title = `Konfirmasi Hapus Mitra`;
                    message =
                        `Anda yakin ingin menghapus <b>${name}</b> dari survei ini? Tindakan ini tidak dapat dibatalkan.`;
                    break;
                case 'hapus_survei':
                    title = 'Konfirmasi Hapus Survei';
                    message =
                        `Apakah Anda yakin ingin menghapus survei <b>${name}</b>? SEMUA MITRA YANG TERKAIT AKAN DIPUTUSKAN RELASINYA.`;
                    formId = 'form-delete-survey';
                    break;
                case 'hapus_semua_mitra':
                    title = 'Konfirmasi Hapus Semua Mitra';
                    message =
                        `Anda yakin ingin menghapus <b>SEMUA MITRA</b> dari survei <b>${name}</b>? Tindakan ini tidak dapat dibatalkan.`;
                    formId = `form-${action}-${id}`;
                    break;
            }

            // Jika ada pesan kustom dari controller (misal: honor limit)
            if (customMessage) {
                message = customMessage;
            }

            // Tampilkan pesan di modal
            modalTitle.textContent = title;
            modalMessage.innerHTML = message;

            // Reset dan sembunyikan area error
            modalErrors.classList.add('hidden');
            modalErrorList.innerHTML = '';

            // Jika ada error validasi dari controller
            if (errors && errors.length > 0) {
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    modalErrorList.appendChild(li);
                });
                modalErrors.classList.remove('hidden');
            }

            // Atur form yang akan disubmit
            confirmButton.dataset.formId = formId;

            // Sembunyikan atau tampilkan input `force_action`
            const formToSubmit = document.getElementById(formId);
            if (formToSubmit) {
                const forceInput = formToSubmit.querySelector('.force-action-input');
                if (forceInput) {
                    // 'force' bernilai true jika ini adalah konfirmasi kedua (honor limit).
                    // Aktifkan input `force_action` agar nilainya terkirim saat form disubmit.
                    forceInput.disabled = !force;
                }
            }

            // Tampilkan modal
            modal.style.display = 'flex';
        }

        // Event listener untuk tombol "Iya, lanjutkan"
        confirmButton.addEventListener('click', () => {
            const formId = confirmButton.dataset.formId;
            if (formId) {
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            }
            modal.style.display = 'none'; // Sembunyikan modal setelah submit
        });

        // Event listener untuk tombol "Batal"
        cancelButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Event listener untuk menutup modal jika klik di luar konten
        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        // Logika untuk menampilkan modal secara otomatis saat halaman dimuat
        // Ini dieksekusi jika controller mengembalikan error atau butuh konfirmasi
        document.addEventListener('DOMContentLoaded', function() {

            // Kasus 1: Validasi Gagal
            @if ($errors->any() && session('show_modal'))
                const actionInfo = "{{ session('show_modal') }}".split('-'); // cth: ['edit', '123']
                const actionType = actionInfo[0];
                const mitraId = actionInfo[1];
                const mitraRow = document.getElementById(`form-${actionType}-${mitraId}`);
                if (mitraRow) {
                    // Ambil nama dari link di baris yang sama untuk pesan error yang lebih baik
                    const mitraName = mitraRow.closest('tr').querySelector('a').textContent.trim();
                    const allErrors = {!! json_encode($errors->all()) !!};
                    showConfirmation(actionType, mitraId, mitraName, false, '', allErrors);
                }
            @endif

            // --- BAGIAN KUNCI: Menangkap data konfirmasi dari Controller ---
            @if (session('show_modal_confirmation'))
                const confirmData = {!! json_encode(session('show_modal_confirmation')) !!};
                showConfirmation(
                    confirmData.type,
                    confirmData.mitra_id,
                    '', // nama tidak perlu karena sudah ada di custom message
                    confirmData.force, // Akan bernilai 'true' dari controller
                    confirmData.message
                );
            @endif


        });
    </script>
</body>

</html>
