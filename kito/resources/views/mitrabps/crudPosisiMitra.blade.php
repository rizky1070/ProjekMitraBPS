<?php
$title = 'Daftar Posisi Mitra';
?>
@include('mitrabps.headerTemp')
<link rel="icon" href="/Logo BPS.png" type="image/png">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@include('mitrabps.cuScroll')

{{-- CSS untuk Modal Konfirmasi Universal --}}
<style>
    .confirmation-modal {
        display: none;
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
        padding: 24px;
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

<body x-data="{ sidebarOpen: false }" class="h-full bg-gray-200">
    {{-- Pesan Sukses/Error Global (dari SweetAlert) --}}
    @if (session('success'))
        <script>
            swal("Berhasil!", "{{ session('success') }}", "success");
        </script>
    @endif
    {{-- Menangani error validasi dari controller --}}
    @if ($errors->any())
        <script>
            const errorMessages = `{!! implode('<br>', $errors->all()) !!}`;
            swal("Error!", errorMessages, "error");
        </script>
    @endif

    {{-- HTML untuk Modal Konfirmasi Universal --}}
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-modal-content">
            <h3 class="text-xl font-bold mb-4" id="modalTitle">Konfirmasi Aksi</h3>
            <div id="modalBody">
                {{-- Konten dinamis (pesan, form) akan dimasukkan di sini oleh JS --}}
            </div>
            <div id="modalErrors" class="mb-4 hidden">
                <p class="font-bold text-red-600">Harap perbaiki error berikut:</p>
                <ul id="modalErrorList" class="error-list"></ul>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button id="cancelButton" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                <button id="confirmButton" class="px-4 py-2 bg-oren text-white rounded hover:bg-orange-500">Iya,
                    Lanjutkan</button>
            </div>
        </div>
    </div>

    <div class="flex h-screen">
        <x-sidebar></x-sidebar>

        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <h1 class="text-2xl font-bold mb-4">Kelola Posisi Mitra</h1>

                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between items-center mb-4">
                        <div class="w-64">
                            <select id="searchSelect" placeholder="Cari posisi...">
                                <option value="">Semua Posisi</option>
                                @foreach ($posisiNames as $name)
                                    <option value="{{ $name }}"
                                        {{ request('search') == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Tombol ini akan memanggil modal universal untuk menambah data --}}
                        <button onclick="showConfirmation('tambah')"
                            class="bg-oren text-white ml-2 px-4 py-2 rounded hover:bg-orange-500 transition"
                            title="Tambah Posisi Mitra Baru">
                            Tambah
                        </button>
                    </div>

                    <div class="cuScrollTableX">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Posisi</th>
                                    <th
                                        class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posisiMitra as $posisi)
                                    <tr class="hover:bg-gray-50" style="border-top-width: 2px; border-color: #D1D5DB;">
                                        <td class="px-4 py-2 text-left">{{ $posisi->nama_posisi }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center">
                                            {{-- Tombol Edit memanggil modal universal --}}
                                            <button
                                                onclick="showConfirmation('edit', {{ $posisi->id_posisi_mitra }}, '{{ addslashes($posisi->nama_posisi) }}')"
                                                class="bg-oren text-white px-3 py-1 rounded-lg hover:bg-orange-500 mr-3">
                                                Edit
                                            </button>

                                            {{-- Tombol Hapus memanggil modal universal, di dalam form --}}
                                            <form id="form-hapus-{{ $posisi->id_posisi_mitra }}"
                                                action="{{ route('posisi.destroy', $posisi->id_posisi_mitra) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="showConfirmation('hapus', {{ $posisi->id_posisi_mitra }}, '{{ addslashes($posisi->nama_posisi) }}')"
                                                    class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4">Tidak ada data posisi ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @include('components.pagination', ['paginator' => $posisiMitra])
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Inisialisasi TomSelect untuk pencarian
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#searchSelect', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                onChange: (value) => {
                    const url = new URL(window.location);
                    if (value) {
                        url.searchParams.set('search', value);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.location.href = url.toString();
                }
            });
        });

        // --- Logika Modal Konfirmasi Universal BARU ---
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const confirmButton = document.getElementById('confirmButton');
        const cancelButton = document.getElementById('cancelButton');
        let currentFormId = null;

        function showConfirmation(action, id = null, name = '') {
            let title = '';
            let bodyContent = '';
            let formHtml = '';

            // Atur pesan dan form dinamis berdasarkan aksi
            switch (action) {
                case 'tambah':
                    title = 'Tambah Posisi Mitra Baru';
                    formHtml = `
                        <form id="form-tambah" action="{{ route('posisi.store') }}" method="POST">
                            @csrf
                            <label for="nama_posisi" class="block text-gray-700 mb-2">Nama Posisi</label>
                            <input type="text" name="nama_posisi" id="nama_posisi_tambah" 
                                class="w-full px-3 py-2 border rounded" required
                                value="{{ old('nama_posisi') }}">
                        </form>
                    `;
                    bodyContent = formHtml;
                    currentFormId = 'form-tambah';
                    break;

                case 'edit':
                    title = 'Edit Posisi Mitra';
                    formHtml = `
                        <p class="mb-4">Silakan ubah nama untuk posisi <b>${name}</b>.</p>
                        <form id="form-edit-${id}" action="/posisimitra/${id}" method="POST">
                            @csrf
                            @method('PUT')
                            <label for="nama_posisi_edit" class="block text-gray-700 mb-2">Nama Posisi Baru</label>
                            <input type="text" name="nama_posisi" id="nama_posisi_edit" 
                                class="w-full px-3 py-2 border rounded" required value="${name}">
                        </form>
                    `;
                    bodyContent = formHtml;
                    currentFormId = `form-edit-${id}`;
                    break;

                case 'hapus':
                    title = 'Konfirmasi Hapus';
                    bodyContent =
                        `<p>Anda yakin ingin menghapus posisi <b>${name}</b>? Tindakan ini tidak dapat dibatalkan.</p>`;
                    currentFormId = `form-hapus-${id}`;
                    break;
            }

            modalTitle.textContent = title;
            modalBody.innerHTML = bodyContent;
            modal.style.display = 'flex';

            // Auto-focus ke input field jika ada
            const inputField = modal.querySelector('input[type="text"]');
            if (inputField) {
                inputField.focus();
            }
        }

        // Event listener untuk tombol "Iya, lanjutkan"
        confirmButton.addEventListener('click', () => {
            if (currentFormId) {
                const form = document.getElementById(currentFormId);
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
    </script>
</body>

</html>
