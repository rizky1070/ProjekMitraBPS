<?php
$title = 'Kelola Survei';
?>
@include('mitrabps.headerTemp')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
{{-- Link ke CSS Tom Select (jika belum ada di headerTemp) --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
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

    /* Fix untuk memastikan input di dalam tabel tidak merusak tinggi baris */
    .table-input,
    .table-select .ts-control {
        vertical-align: middle;
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

    {{-- Modal Konfirmasi Universal --}}
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-modal-content">
            <h3 class="text-lg font-bold mb-4" id="modalTitle">Konfirmasi Aksi</h3>
            <div id="modalBody">
                <p id="modalMessage" class="mb-4">Apakah Anda yakin ingin melanjutkan?</p>
                <div id="modalErrors" class="mb-4 hidden">
                    <p class="font-bold text-red-600">Harap perbaiki error berikut:</p>
                    <ul id="modalErrorList" class="error-list"></ul>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button id="cancelButton" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                <button id="confirmButton" class="px-4 py-2 bg-oren text-white rounded hover:bg-orange-500">Iya,
                    Lanjutkan</button>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-x-hidden bg-gray-200">
        {{-- Tombol Kembali --}}
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
                <form action="{{ route('survey.delete', ['id_survei' => $survey->id_survei]) }}" method="POST"
                    id="form-delete-survey">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="showConfirmation('hapus_survei', 0, '{{ $survey->nama_survei }}')"
                        class="mt-4 px-4 py-2 bg-red-500 text-white font-medium rounded-md hover:bg-red-600">Hapus
                        Survei</button>
                </form>
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
                        <p><strong>KRO :</strong> {{ $survey->kro }} </p>
                    </div>
                </div>
                <div class="flex items-center">
                    <p><strong>Status :</strong>
                        <span class="font-bold">
                            @if ($survey->status_survei == 1)
                                <div class="bg-red-500 text-white px-2 py-1 rounded ml-2 mr-5">Belum Dikerjakan</div>
                            @elseif($survey->status_survei == 2)
                                <div class="bg-yellow-300 text-white px-2 py-1 rounded ml-2 mr-5">Sedang Dikerjakan
                                </div>
                            @elseif($survey->status_survei == 3)
                                <div class="bg-green-500 text-white px-2 py-1 rounded ml-2 mr-5">Sudah Dikerjakan</div>
                            @else<span class="bg-gray-500 text-white rounded-md px-2 py-1 ml-2">Status Tidak
                                    Diketahui</span>
                            @endif
                        </span>
                    </p>
                </div>
            </div>
            <div class="flex justify-between items-center mb-4 mt-6">
                <h3 class="text-xl font-bold">Daftar Mitra</h3>
                <div class="flex gap-2">
                    <a href="{{ route('mitraSurvei.export.excel', ['id_survei' => $survey->id_survei]) }}"
                        class="px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-all duration-300">Export
                        Excel</a>
                    @if ($survey->mitra_survei_count > 0)
                        <form action="{{ route('survey.deleteAllMitra', ['id_survei' => $survey->id_survei]) }}"
                            method="POST" id="form-hapus_semua_mitra-{{ $survey->id_survei }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                onclick="showConfirmation('hapus_semua_mitra', {{ $survey->id_survei }}, '{{ $survey->nama_survei }}')"
                                class="px-4 py-2 bg-red-800 text-white font-medium rounded-md hover:bg-red-900">Hapus
                                Semua Mitra</button>
                        </form>
                    @endif
                    <button type="button"
                        class="px-4 py-2 bg-oren rounded-md text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300"
                        onclick="openModal()">+ Tambah</button>
                </div>
            </div>
            <div class="cuScrollFilter bg-white rounded-lg shadow-sm p-6 mb-6">
                <form id="filterForm" action="{{ route('editSurvei.filter', ['id_survei' => $survey->id_survei]) }}"
                    method="GET" class="space-y-4">
                    <div class="flex items-center relative">
                        <label for="nama_lengkap" class="w-32 text-lg font-semibold text-gray-800">Cari
                            Mitra</label>
                        <select name="nama_lengkap" id="nama_mitra"
                            class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                            {{ empty($namaMitraOptions) ? 'disabled' : '' }}>
                            <option value="">Semua Mitra</option>
                            @foreach ($namaMitraOptions as $nama => $label)
                                <option value="{{ $nama }}" @if (request('nama_lengkap') == $nama) selected @endif>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">Filter Mitra</h4>
                    </div>
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
                        <div class="flex items-center">
                            <label for="kecamatan" class="w-32 text-sm font-medium text-gray-700">Kecamatan</label>
                            <select name="kecamatan" id="kecamatan"
                                class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                                {{ empty($kecamatanOptions) ? 'disabled' : '' }}>
                                <option value="">Semua Kecamatan</option>
                                @foreach ($kecamatanOptions as $kecam)
                                    <option value="{{ $kecam->id_kecamatan }}"
                                        @if (request('kecamatan') == $kecam->id_kecamatan) selected @endif>
                                        [{{ $kecam->kode_kecamatan }}] {{ $kecam->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            {{-- BAGIAN TABEL YANG SUDAH DIPERBAIKI --}}
            <div class="border rounded-lg shadow-sm bg-white p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y-2 divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                {{-- Penambahan class lebar (w-xx) untuk menstabilkan kolom --}}
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Nama Mitra</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    Domisili</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                    Total Survei</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                    Mitra Tahun</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Vol</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                                    Rate Honor</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    Posisi</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $errorMitraId = session('show_modal')
                                    ? (int) (explode('-', session('show_modal'))[1] ?? 0)
                                    : null;
                            @endphp
                            @forelse ($mitras as $mitra)
                                @if ($mitra->isFollowingSurvey)
                                    <form
                                        action="{{ route('mitra.update', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}"
                                        method="POST" id="form-edit-{{ $mitra->id_mitra }}" class="hidden">
                                        @csrf<input type="hidden" name="force_action" value="1"
                                            class="force-action-input"></form>
                                    <form
                                        action="{{ route('mitra.delete', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}"
                                        method="POST" id="form-hapus-{{ $mitra->id_mitra }}" class="hidden">@csrf
                                        @method('DELETE')</form>
                                @else
                                    <form
                                        action="{{ route('mitra.toggle', ['id_survei' => $survey->id_survei, 'id_mitra' => $mitra->id_mitra]) }}"
                                        method="POST" id="form-tambah-{{ $mitra->id_mitra }}" class="hidden">
                                        @csrf<input type="hidden" name="force_action" value="1"
                                            class="force-action-input"></form>
                                @endif

                                <tr id="baris-mitra-{{ $mitra->id_mitra }}" class="hover:bg-gray-50">
                                    {{-- Kolom dengan style seragam --}}
                                    <td class="text-sm font-medium text-gray-900 whitespace-normal break-words"
                                        style="max-width: 120px;">
                                        <div class="ml-3 flex justify-center items-center text-center">
                                            <a href="/profilMitra/{{ $mitra->id_mitra }}"
                                                class="hover:underline transition duration-300 ease-in-out"
                                                style="text-decoration-color: #FFA500; text-decoration-thickness: 3px;">
                                                {{ $mitra->nama_lengkap }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        {{ $mitra->kecamatan->nama_kecamatan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        {{ $mitra->total_survei }}
                                    </td>
                                    <td class="text-center whitespace-normal break-words" style="max-width: 120px;">
                                        {{ \Carbon\Carbon::parse($mitra->tahun)->translatedFormat('Y') }}
                                    </td>
                                    {{-- Kolom input dan aksi yang TIDAK diubah stylenya (kecuali perataan vertikal) --}}
                                    @if ($mitra->isFollowingSurvey)
                                        <td class="p-2 align-middle">
                                            <input type="number" name="vol"
                                                form="form-edit-{{ $mitra->id_mitra }}"
                                                value="{{ $errorMitraId == $mitra->id_mitra ? old('vol') : $mitra->vol }}"
                                                class="table-input w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                                placeholder="Vol">
                                        </td>
                                        <td class="p-2 align-middle">
                                            <input type="number" name="rate_honor"
                                                form="form-edit-{{ $mitra->id_mitra }}"
                                                value="{{ $errorMitraId == $mitra->id_mitra ? old('rate_honor') : $mitra->rate_honor }}"
                                                class="table-input w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                                placeholder="Rate">
                                        </td>
                                        <td class="p-2 align-middle table-select">
                                            <select name="id_posisi_mitra" data-mitra-id="{{ $mitra->id_mitra }}"
                                                form="form-edit-{{ $mitra->id_mitra }}" class="w-full">
                                                <option value="">Pilih Posisi</option>
                                                @foreach ($posisiMitraOptions as $posisi)
                                                    <option value="{{ $posisi->id_posisi_mitra }}"
                                                        @if (($errorMitraId == $mitra->id_mitra ? old('id_posisi_mitra') : $mitra->id_posisi_mitra) == $posisi->id_posisi_mitra) selected @endif>
                                                        {{ $posisi->nama_posisi }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2 align-middle">
                                            <div class="grid grid-cols-2 gap-2 w-full">
                                                <button type="button"
                                                    onclick="showConfirmation('edit', {{ $mitra->id_mitra }}, '{{ $mitra->nama_lengkap }}')"
                                                    class="flex justify-center items-center p-2 bg-oren text-white rounded-md hover:bg-orange-600 transition-all"
                                                    title="Simpan"><svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg></button>
                                                <button type="button"
                                                    onclick="showConfirmation('hapus', {{ $mitra->id_mitra }}, '{{ $mitra->nama_lengkap }}')"
                                                    class="flex justify-center items-center p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-all"
                                                    title="Hapus"><svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg></button>
                                            </div>
                                        </td>
                                    @else
                                        <td class="p-2 align-middle">
                                            <input type="number" name="vol"
                                                form="form-tambah-{{ $mitra->id_mitra }}"
                                                value="{{ $errorMitraId == $mitra->id_mitra ? old('vol') : '' }}"
                                                class="table-input w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                                placeholder="Vol">
                                        </td>
                                        <td class="p-2 align-middle">
                                            <input type="number" name="rate_honor"
                                                form="form-tambah-{{ $mitra->id_mitra }}"
                                                value="{{ $errorMitraId == $mitra->id_mitra ? old('rate_honor') : '' }}"
                                                class="table-input w-full p-2 text-center border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 text-sm"
                                                placeholder="Rate">
                                        </td>
                                        <td class="p-2 align-middle table-select">
                                            <select name="id_posisi_mitra" data-mitra-id="{{ $mitra->id_mitra }}"
                                                form="form-tambah-{{ $mitra->id_mitra }}" class="w-full">
                                                <option value="">Pilih Posisi</option>
                                                @foreach ($posisiMitraOptions as $posisi)
                                                    <option value="{{ $posisi->id_posisi_mitra }}"
                                                        @if ($errorMitraId == $mitra->id_mitra && old('id_posisi_mitra') == $posisi->id_posisi_mitra) selected @endif>
                                                        {{ $posisi->nama_posisi }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2 align-middle">
                                            <button type="button"
                                                onclick="showConfirmation('tambah', {{ $mitra->id_mitra }}, '{{ $mitra->nama_lengkap }}')"
                                                class="w-full py-2 px-4 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition-all">Tambah</button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-gray-500">Tidak ada data mitra
                                        untuk ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @include('components.pagination', ['paginator' => $mitras])
        </div>
    </main>

    {{-- Modal Upload --}}
    <div id="uploadModal"
        class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Import Mitra ke Survei</h2>
            <p class="mb-4 text-sm text-red-600 font-bold">Pastikan format file excel yang diimport sesuai!</p>
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('upload.excel', ['id_survei' => $survey->id_survei]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none mb-2">
                <p class="py-2 text-sm">Belum punya template? <a href="{{ asset('addMitra2Survey.xlsx') }}"
                        class="text-blue-500 hover:text-blue-600 font-bold" download>Download di sini.</a></p>
                <div class="flex justify-end mt-4 space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md font-medium hover:bg-gray-600"
                        onclick="closeModal()">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-oren rounded-md text-white font-medium hover:bg-orange-500">Unggah</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        function openModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }

        const modal = document.getElementById('confirmationModal'),
            modalTitle = document.getElementById('modalTitle'),
            modalMessage = document.getElementById('modalMessage'),
            modalErrors = document.getElementById('modalErrors'),
            modalErrorList = document.getElementById('modalErrorList'),
            confirmButton = document.getElementById('confirmButton'),
            cancelButton = document.getElementById('cancelButton');

        function showConfirmation(action, id, name, force = false, customMessage = '', errors = null) {
            let title, message, formId = `form-${action}-${id}`;
            switch (action) {
                case 'tambah':
                    title = 'Konfirmasi Tambah Mitra';
                    message = `Anda yakin ingin menambahkan <b>${name}</b> ke survei ini?`;
                    break;
                case 'edit':
                    title = 'Konfirmasi Simpan Perubahan';
                    message = `Anda yakin ingin menyimpan perubahan untuk mitra <b>${name}</b>?`;
                    break;
                case 'hapus':
                    title = 'Konfirmasi Hapus Mitra';
                    message = `Anda yakin ingin menghapus <b>${name}</b> dari survei ini?`;
                    break;
                case 'hapus_survei':
                    title = 'Konfirmasi Hapus Survei';
                    message = `Yakin ingin menghapus survei <b>${name}</b>? SEMUA relasi mitra akan ikut terhapus.`;
                    formId = 'form-delete-survey';
                    break;
                case 'hapus_semua_mitra':
                    title = 'Konfirmasi Hapus Semua Mitra';
                    message = `Yakin ingin menghapus <b>SEMUA MITRA</b> dari survei <b>${name}</b>?`;
                    break;
            }
            if (customMessage) {
                message = customMessage;
            }
            modalTitle.textContent = title;
            modalMessage.innerHTML = message;
            modalErrors.classList.add('hidden');
            modalErrorList.innerHTML = '';
            if (errors && errors.length > 0) {
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    modalErrorList.appendChild(li);
                });
                modalErrors.classList.remove('hidden');
            }
            confirmButton.dataset.formId = formId;
            const formToSubmit = document.getElementById(formId);
            if (formToSubmit) {
                const forceInput = formToSubmit.querySelector('.force-action-input');
                if (forceInput) {
                    forceInput.disabled = !force;
                }
            }
            modal.style.display = 'flex';
        }
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
        cancelButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });
        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk inisialisasi semua TomSelect
            function initTomSelect() {
                // Inisialisasi TomSelect untuk filter utama
                if (document.querySelector('#nama_mitra')) {
                    new TomSelect('#nama_mitra', {
                        placeholder: 'Cari Mitra',
                        searchField: 'text'
                    });
                }
                if (document.querySelector('#tahun')) {
                    new TomSelect('#tahun', {
                        placeholder: 'Pilih Tahun',
                        searchField: 'text'
                    });
                }
                if (document.querySelector('#bulan')) {
                    new TomSelect('#bulan', {
                        placeholder: 'Pilih Bulan',
                        searchField: 'text'
                    });
                }
                if (document.querySelector('#kecamatan')) {
                    new TomSelect('#kecamatan', {
                        placeholder: 'Pilih Kecamatan',
                        searchField: 'text'
                    });
                }

                // Inisialisasi TomSelect untuk dropdown posisi di dalam tabel
                document.querySelectorAll('select[name="id_posisi_mitra"]').forEach(selectEl => {
                    new TomSelect(selectEl, {
                        placeholder: 'Pilih Posisi',
                        searchField: 'text',
                        // Kunci agar dropdown tidak merusak layout tabel
                        dropdownParent: 'body'
                    });
                });
            }

            // Panggil fungsi inisialisasi
            initTomSelect();

            // Auto submit filter form
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                let timeout;
                filterForm.addEventListener('change', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        filterForm.submit();
                    }, 500);
                });
            }

            // Logika untuk menampilkan modal error setelah validasi gagal
            @if ($errors->any() && session('show_modal'))
                const actionInfo = "{{ session('show_modal') }}".split('-'),
                    actionType = actionInfo[0],
                    mitraId = actionInfo[1];
                const mitraRow = document.getElementById(`baris-mitra-${mitraId}`);
                if (mitraRow) {
                    const mitraLink = mitraRow.querySelector('a'),
                        mitraName = mitraLink ? mitraLink.textContent.trim() : 'Mitra';
                    const allErrors = {!! json_encode($errors->all()) !!};
                    showConfirmation(actionType, mitraId, mitraName, false, '', allErrors);
                }
            @endif
            @if (session('show_modal_confirmation'))
                const confirmData = {!! json_encode(session('show_modal_confirmation')) !!};
                showConfirmation(confirmData.type, confirmData.mitra_id, '', true, confirmData.message);
            @endif
        });
    </script>
</body>

</html>
