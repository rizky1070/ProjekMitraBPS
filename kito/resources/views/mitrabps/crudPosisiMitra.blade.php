<?php
$title = 'Daftar Posisi Mitra';
?>
@include('mitrabps.headerTemp')
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="icon" href="/Logo BPS.png" type="image/png">
@include('mitrabps.cuScroll')
</head>
<body class="h-full">
    <div x-data="PosisiData()" x-init="sidebarOpen = false" x-bind="sidebarOpen" class="flex h-screen">
        <x-sidebar></x-sidebar>
        
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="flex justify-between mb-4">
                    <h1 class="text-2xl font-bold mb-4">Kelola Posisi Mitra</h1>
                </div>
                
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex justify-between w-64">
                            <select id="searchSelect" placeholder="Cari posisi..." class="w-full">
                                <option value="">Semua Nama</option>
                                @foreach($posisiNames as $name)
                                    <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button @click="showAddModal = true; newPosisiName = ''; newRateHonor = ''" 
                            class="bg-oren text-white ml-2 px-4 py-2 rounded hover:bg-orange-500 transition"
                            title="Tambah Posisi Mitra Baru">
                            Tambah
                        </button>
                    </div>
                    
                    <!-- Table Structure -->
                    <div class="cuScrollTableX">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="min-w-full divide-y divide-gray-200">
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Posisi</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rate Honor</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($posisiMitra as $Posisi)
                                <tr class="hover:bg-gray-50" style=" border-top-width: 2px; border-color: #D1D5DB;">
                                    <td class="px-4 py-2 text-left">{{ $Posisi->nama_posisi }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">Rp {{ number_format($Posisi->rate_honor, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <!-- Edit Button -->
                                        <button @click="showEditModal = true; currentPosisi = {{ $Posisi->id_posisi_mitra }}; editPosisiName = '{{ $Posisi->nama_posisi }}'; editRateHonor = '{{ $Posisi->rate_honor }}'"
                                            class="bg-oren text-white px-3 py-1 rounded-lg hover:bg-orange-500 mr-3">
                                            Edit
                                        </button>
                                        <!-- Delete Button -->
                                        <button @click="deletePosisi({{ $Posisi->id_posisi_mitra }}, '{{ $Posisi->nama_posisi }}')"
                                            class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @include('components.pagination', ['paginator' => $posisiMitra])
            </main>
        </div>
        
        <!-- Add Posisi Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Tambah Posisi Mitra Baru</h2>
                <form @submit.stop.prevent="submitAddForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="newPosisiName">Nama Posisi</label>
                        <input x-model="newPosisiName" type="text" id="newPosisiName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="newRateHonor">Rate Honor</label>
                        <input x-model="newRateHonor" type="number" id="newRateHonor" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showAddModal = false" 
                            class="px-4 py-2 border rounded hover:bg-gray-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Edit Posisi Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Edit Posisi Mitra</h2>
                <form @submit.stop.prevent="submitEditForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editPosisiName">Nama Posisi</label>
                        <input x-model="editPosisiName" type="text" id="editPosisiName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editRateHonor">Rate Honor</label>
                        <input x-model="editRateHonor" type="number" id="editRateHonor" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showEditModal = false" 
                            class="px-4 py-2 border rounded hover:bg-gray-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('PosisiData', () => ({
            sidebarOpen: false,
            showAddModal: false,
            showEditModal: false,
            currentPosisi: null,
            newPosisiName: '',
            newRateHonor: '',
            editPosisiName: '',
            editRateHonor: '',
            isLoading: false,
            
            init() {
                new TomSelect('#searchSelect', {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    maxOptions: null,
                    onChange: (value) => {
                        const params = new URLSearchParams();
                        if (value) params.append('search', value);
                        window.location.href = window.location.pathname + '?' + params.toString();
                    }
                });
            },
            
            async submitAddForm() {
                this.isLoading = true;
                try {
                    const response = await fetch('/posisimitra', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            nama_posisi: this.newPosisiName,
                            rate_honor: this.newRateHonor
                        })
                    });
                    
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal menambahkan posisi');
                    
                    Swal.fire("Berhasil!", "Posisi mitra baru telah ditambahkan", "success")
                        .then(() => window.location.reload());
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                } finally {
                    this.isLoading = false;
                    this.showAddModal = false;
                }
            },
            
            async submitEditForm() {
                this.isLoading = true;
                try {
                    const response = await fetch(`/posisimitra/${this.currentPosisi}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            nama_posisi: this.editPosisiName,
                            rate_honor: this.editRateHonor
                        })
                    });
                    
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal memperbarui posisi');
                    
                    Swal.fire("Berhasil!", "Posisi mitra telah diperbarui", "success")
                        .then(() => window.location.reload());
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                } finally {
                    this.isLoading = false;
                    this.showEditModal = false;
                }
            },
            
            async deletePosisi(id, nama_posisi) {
                try {
                    const result = await Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: `Anda akan menghapus Posisi mitra "${nama_posisi}"`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, hapus!"
                    });
                    
                    if (result.isConfirmed) {
                        const response = await fetch(`/posisimitra/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Gagal menghapus posisi');
                        
                        Swal.fire("Berhasil!", `Posisi "${nama_posisi}" telah dihapus`, "success")
                            .then(() => window.location.reload());
                    }
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                }
            }
        }));
    });
    </script>
</body>
</html>

