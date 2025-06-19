<?php
$title = 'Profil Mitra';
?>
@include('mitrabps.headerTemp')
@include('mitrabps.cuScroll')
<style>
    /* Style untuk modal konfirmasi universal (sama seperti sebelumnya) */
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
</style>
</head>

<body class="h-full bg-gray-200">
    @if (session('success'))
        <script>
            swal("Success!", "{{ session('success') }}", "success");
        </script>
    @endif

    {{-- Jika ada error validasi (untuk masa depan), swal akan menampilkannya --}}
    @if ($errors->any())
        <script>
            swal("Error!", "{{ $errors->first() }}", "error");
        </script>
    @endif

    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-modal-content">
            <h3 class="text-lg font-bold mb-4" id="modalTitle">Konfirmasi Aksi</h3>
            <div id="modalBody">
                <p id="modalMessage" class="mb-4">Apakah Anda yakin ingin melanjutkan?</p>
                {{-- Area untuk menampilkan error validasi (jika ada) --}}
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
    <main class="cuScrollGlobalY flex-1 overflow-x-hidden bg-gray-200">
        <a href="{{ url('/daftarMitra') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-oren hover:bg-orange-500 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="max-w-4xl mx-auto mt-4">
            <h1 class="text-2xl font-bold">Profil Mitra</h1>
            <div class="flex flex-col md:flex-row items-center bg-white my-4 px-6 py-5 rounded-lg shadow">
                <div class="flex flex-col justify-center items-center text-center mb-4 md:mb-0">
                    <img alt="Profile picture"
                        class="w-32 rounded-full border-4 object-cover {{ $mits->jenis_kelamin == 2 ? 'border-pink-500' : 'border-blue-500' }}"
                        src="{{ $profileImage }}" onerror="this.onerror=null;this.src='{{ asset('person.png') }}'"
                        width="100" height="100">

                    <h2 class="text-xl font-bold mt-2">{{ $mits->nama_lengkap }}</h2>
                    <h5 class=" my-2">{{ $mits->sobat_id }}</h5>
                    @if (auth()->user()->is_admin || auth()->user()->is_leader)
                    <form action="{{ route('mitra.destroy', $mits->id_mitra) }}" method="POST" id="form-hapus_mitra">
                        @csrf
                        @method('DELETE')
                        {{-- Tombol diubah menjadi type="button" dan memanggil JS --}}
                        <button type="button"
                            onclick="showConfirmation('hapus_mitra', {{ $mits->id_mitra }}, '{{ $mits->nama_lengkap }}')"
                            class="bg-red-500 text-white font-medium px-3 py-1 rounded-md hover:bg-red-600 transition-all duration-200"
                            aria-label="Hapus mitra" title="Hapus permanen mitra ini">
                            Hapus
                        </button>
                    </form>
                    @endif
                </div>

                <div class="md:pl-6 w-full">
                    {{-- Detail Info Mitra (Domisili, Alamat, dll. tidak berubah) --}}
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Domisili :</strong>
                        <span class="text-right">{{ $mits->kecamatan->nama_kecamatan }},
                            {{ $mits->kabupaten->nama_kabupaten }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Alamat Detail :</strong>
                        <span class="text-right">{{ $mits->alamat_mitra }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Nomor Handphone :</strong>
                        <span class="text-right">{{ $mits->no_hp_mitra }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Email :</strong>
                        <span class="text-right">{{ $mits->email_mitra }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Mitra Tahun :</strong>
                        <span class="text-right">{{ \Carbon\Carbon::parse($mits->tahun)->translatedFormat('Y') }}
                    </div>

                    {{-- Form untuk update detail pekerjaan --}}
                    <div class="flex justify-between items-center w-full border-b py-2">
                        <strong>Pekerjaan :</strong>
                        @if (auth()->user()->is_admin || auth()->user()->is_leader)
                        <form action="{{ route('mitra.updateDetailPekerjaan', $mits->id_mitra) }}" method="POST"
                            id="form-simpan_pekerjaan" class="flex items-center gap-2 flex-1 ml-4">
                            @csrf
                            @method('PUT')
                            <div class="flex-1">
                                {{-- INPUT YANG DIUBAH STYLENYA --}}
                                <input type="text" name="detail_pekerjaan" value="{{ $mits->detail_pekerjaan }}"
                                    class="w-full p-2 text-right border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    placeholder="Masukkan detail pekerjaan" title="Ubah detail pekerjaan">
                            </div>
                            <button type="button"
                                onclick="showConfirmation('simpan_pekerjaan', {{ $mits->id_mitra }}, '{{ $mits->nama_lengkap }}')"
                                class="bg-oren text-white px-3 py-2 rounded-md font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300"
                                aria-label="Simpan detail pekerjaan" title="Klik untuk menyimpan detail pekerjaan">
                                Simpan
                            </button>
                        </form>
                        @else
                        <span class="text-right">{{ $mits->detail_pekerjaan }}</span>
                        @endif
                    </div>

                    {{-- Form untuk update status --}}
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Status :</strong>
                        @if (auth()->user()->is_admin || auth()->user()->is_leader)
                        <form action="{{ route('mitra.updateStatus', $mits->id_mitra) }}" method="POST"
                            id="form-ubah_status">
                            @csrf
                            @method('PUT')
                            @php
                                $isActive = $mits->status_pekerjaan == 1;
                                $colorClasses = $isActive
                                    ? 'bg-green-500' // AKTIF -> warna hijau
                                    : 'bg-red-500'; // NON-AKTIF -> warna merah
                                $hoverColor = $isActive ? 'hover:bg-green-600' : 'hover:bg-red-600';
                                $buttonText = $isActive ? 'Aktif' : 'Non-Aktif';
                                // Teks untuk tombol berikutnya (aksi yang akan dilakukan)
                                $actionText = $isActive ? 'Non-Aktifkan' : 'Aktifkan';
                            @endphp

                            {{-- Tombol diubah menjadi type="button" dan memanggil JS --}}
                            <button type="button" {{-- Kirim status saat ini (0 atau 1) ke fungsi JS --}}
                                onclick="showConfirmation('ubah_status', {{ $mits->id_mitra }}, '{{ $mits->nama_lengkap }}', {{ $mits->status_pekerjaan }})"
                                class="{{ $colorClasses }} {{ $hoverColor }} transition-all duration-300 relative group mt-4 px-4 py-2 text-white font-medium rounded-md"
                                aria-label="Ubah status pekerjaan"
                                title="Klik untuk mengubah status menjadi {{ $actionText }}">
                                {{ $buttonText }}
                            </button>
                        </form>
                        @else
                        <span class="text-right">
                            @if ($mits->status_pekerjaan == 1)
                                <span class="text-white bg-green-500 mt-4 px-2 py-0.5 font-medium rounded-md">Aktif</span>
                            @else
                                <span class="text-white bg-red-500 mt-4 px-2 py-0.5 font-medium rounded-md">Non-Aktif</span>
                            @endif
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Tabel Survei -->
        <div class="max-w-4xl mx-auto">
            <h2 class="text-xl font-bold mb-4">Survei yang diikuti mitra</h2>
            <div id="survei-dikerjakan" class="cuScrollFilter bg-white rounded-lg shadow-sm p-6 mb-6">
                <!-- Form Filter -->
                <form method="GET"
                    action="{{ route('profilMitra.filter', ['id_mitra' => $mits->id_mitra, 'scroll_to' => request('scroll_to')]) }}"
                    class="flex flex-wrap gap-4 items-center mb-2" id="filterForm">
                    <!-- Survey Name Row -->
                    <div class="flex flex-col md:flex-row items-start md:items-center">
                        <label for="nama_survei"
                            class="w-full md:w-32 text-sm md:text-lg font-semibold text-gray-800 mb-1 md:mb-0">Cari
                            Survei</label>
                        <select name="nama_survei" id="nama_survei"
                            class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 md:ml-2"
                            {{ empty($namaSurveiOptions) ? 'disabled' : '' }}>
                            <option value="">Semua Survei</option>
                            @foreach ($namaSurveiOptions as $nama => $label)
                                <option value="{{ $nama }}" @if (request('nama_survei') == $nama) selected @endif>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Filter Survei</h2>
                        </div>
                        <div class="flex">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                                <!-- Year Row -->
                                <div class="flex items-center">
                                    <label for="tahun"
                                        class="w-full md:w-32 text-sm font-medium text-gray-700">Tahun</label>
                                    <select name="tahun" id="tahun"
                                        class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                        <option value="">Semua Tahun</option>
                                        @foreach ($tahunOptions as $year => $yearLabel)
                                            <option value="{{ $year }}"
                                                @if (request('tahun') == $year) selected @endif>
                                                {{ $yearLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Month Row -->
                                <div class="flex items-center">
                                    <label for="bulan"
                                        class="w-full md:w-32 text-sm font-medium text-gray-700">Bulan</label>
                                    <select name="bulan" id="bulan"
                                        class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                                        {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                        <option value="">Semua Bulan</option>
                                        @foreach ($bulanOptions as $month => $monthName)
                                            <option value="{{ $month }}"
                                                @if (request('bulan') == $month) selected @endif>
                                                {{ $monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="pl-4 mb-2">
                    @if ($showTotalGaji)
                        <p class="font-bold">Total Gaji Mitra Bulan ini:</p>
                        <p class="text-xl font-bold">Rp {{ number_format($totalGaji, 0, ',', '.') }},00</p>
                    @else
                        <p class="font-sm text-gray-500">*Aktifkan filter bulan untuk melihat total gaji</p>
                    @endif
                </div>
                <div class="bg-white p-4 border border-gray-300 rounded-lg shadow-lg">
                    <!-- Survei yang sudah dikerjakan -->
                    <div class="overflow-x-auto mb-4 pb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Survei yang sudah dikerjakan:</h2>
                        @php
                            $survei_dikerjakan = $survei->filter(fn($s) => $s->survei->status_survei == 3);
                        @endphp
                        @if ($survei_dikerjakan->isEmpty())
                            <h2 class="text-l text-gray-600 pl-4">Tidak ada survei yang sudah dikerjakan</h2>
                        @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Survei</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jadwal Survei</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vol</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rate Honor</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Catatan</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nilai</th>
                                        @if (auth()->user()->is_admin || auth()->user()->is_leader)
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($survei_dikerjakan as $sur)
                                        <tr class="hover:bg-gray-50"
                                            style="border-top-width: 2px; border-color: #D1D5DB;">
                                            <td class="text-sm font-medium text-gray-900 whitespace-normal break-words"
                                                style="max-width: 120px;">
                                                <div class="ml-3 flex text-left">
                                                    <a href="/editSurvei/{{ $sur->survei->id_survei }}"
                                                        class="hover:underline transition duration-300 ease-in-out"
                                                        style="text-decoration-color: #FFA500; text-decoration-thickness: 3px;">
                                                        {{ $sur->survei->nama_survei }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="text-center whitespace-normal break-words"
                                                style="max-width: 200px;">
                                                {{ \Carbon\Carbon::parse($sur->survei->jadwal_kegiatan)->translatedFormat('j F Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($sur->survei->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $sur->vol ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                Rp{{ number_format($sur->rate_honor ?? 0, 0, ',', '.') }}</td>
                                            @if ($sur->catatan == null && $sur->nilai == null)
                                                <td class="p-2 text-center text-red-700 font-bold">Tidak ada catatan
                                                </td>
                                                <td class="p-2 text-center text-red-700 font-bold">Belum dinilai</td>
                                            @else
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    {{ $sur->catatan }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    {{ str_repeat('⭐', $sur->nilai) }}</td>
                                            @endif
                                            @if (auth()->user()->is_admin || auth()->user()->is_leader)
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="/penilaianMitra/{{ $sur->mitra->id_mitra }}/{{ $sur->survei->id_survei }}"
                                                    class="px-4 py-1 bg-oren rounded-md text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300">Edit</a>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <!-- Survei yang belum/sedang dikerjakan -->
                    <div class="overflow-x-auto pt-4" style="border-top-width: 2px; border-color: #9CA3AF;">
                        <h2 class="text-lg font-semibold text-gray-800">Survei yang belum/sedang dikerjakan:</h2>
                        @php
                            $survei_belum = $survei->filter(fn($s) => $s->survei->status_survei != 3);
                        @endphp
                        @if ($survei_belum->isEmpty())
                            <h2 class="text-l text-gray-600 pl-5">Tidak ada survei yang belum/sedang dikerjakan</h2>
                        @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Survei</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jadwal Survei</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vol</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rate Honor</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($survei_belum as $sur)
                                        <tr class="hover:bg-gray-50"
                                            style="border-top-width: 2px; border-color: #D1D5DB;">
                                            <td class="text-sm font-medium text-gray-900 whitespace-normal break-words"
                                                style="max-width: 120px;">
                                                <div class="ml-3 flex text-left">
                                                    <a href="/editSurvei/{{ $sur->survei->id_survei }}"
                                                        class="hover:underline transition duration-300 ease-in-out"
                                                        style="text-decoration-color: #FFA500; text-decoration-thickness: 3px;">
                                                        {{ $sur->survei->nama_survei }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="text-center whitespace-normal break-words"
                                                style="max-width: 200px;">
                                                {{ \Carbon\Carbon::parse($sur->survei->jadwal_kegiatan)->translatedFormat('j F Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($sur->survei->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $sur->vol ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                Rp{{ number_format($sur->rate_honor ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- JavaScript Tom Select -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <!-- Inisialisasi Tom Select -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect('#nama_survei', {
                    placeholder: 'Pilih Survei',
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

                // Auto submit saat filter berubah
                const filterForm = document.getElementById('filterForm');
                const tahunSelect = document.getElementById('tahun');
                const bulanSelect = document.getElementById('bulan');
                const surveiSelect = document.getElementById('nama_survei');

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
                surveiSelect.addEventListener('change', submitForm);
            });
        </script>

        </div>
    </main>
    <script>
        // Ambil elemen-elemen modal
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalErrors = document.getElementById('modalErrors');
        const modalErrorList = document.getElementById('modalErrorList');
        const confirmButton = document.getElementById('confirmButton');
        const cancelButton = document.getElementById('cancelButton');

        // Fungsi untuk menampilkan modal konfirmasi. 
        // Parameter `data` digunakan untuk status
        function showConfirmation(action, id, name, data = null) {
            let title = '';
            let message = '';
            let formId = `form-${action}`; // ID form dibuat lebih simpel

            // Atur pesan default berdasarkan aksi
            switch (action) {
                case 'hapus_mitra':
                    title = `Konfirmasi Hapus Mitra`;
                    message =
                        `Anda yakin ingin menghapus <b>${name}</b> secara permanen? SEMUA DATA YANG TERKAIT AKAN DIHAPUS.`;
                    break;
                case 'simpan_pekerjaan':
                    title = `Konfirmasi Simpan Pekerjaan`;
                    message = `Anda yakin ingin menyimpan perubahan detail pekerjaan untuk <b>${name}</b>?`;
                    break;
                case 'ubah_status':
                    // `data` di sini adalah status saat ini (1=aktif, 0=non-aktif)
                    const targetStatusText = (data == 1) ? 'Non-Aktif' : 'Aktif';
                    title = `Konfirmasi Ubah Status`;
                    message = `Anda yakin ingin mengubah status <b>${name}</b> menjadi <b>${targetStatusText}</b>?`;
                    break;
            }

            modalTitle.textContent = title;
            modalMessage.innerHTML = message;

            modalErrors.classList.add('hidden');
            modalErrorList.innerHTML = '';

            // Atur form yang akan disubmit
            confirmButton.dataset.formId = formId;

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
            modal.style.display = 'none';
        });

        // Event listener untuk tombol "Batal" dan klik di luar modal
        cancelButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        // Tidak ada logika `DOMContentLoaded` untuk menampilkan error di modal
        // karena form-form ini tidak memiliki validasi yang kompleks di controller.
        // Jika nanti ditambahkan, logikanya bisa dicopy dari solusi sebelumnya.
    </script>


</body>
@if (request('scroll_to') == 'survei-dikerjakan')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cari elemen yang ingin di-scroll
            const element = document.querySelector('.cuScrollFilter');
            if (element) {
                // Scroll ke elemen dengan offset untuk header
                window.scrollTo({
                    top: element.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    </script>
@endif
<!-- ⭐⭐⭐⭐⭐ -->

</html>
