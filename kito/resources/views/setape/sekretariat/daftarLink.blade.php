<?php
$title = 'Sekretariat';
?>
@include('mitrabps.headerTemp')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
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
    
    @if (session('error'))
    <script>
    swal("Error!", "{{ session('error') }}", "error");
    </script>
    @endif
    
    <div x-data="sekretariatData" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-end mb-4">
                        <button @click="showAddModal = true; resetForm()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Tambah Link Sekretariat
                        </button>
                    </div>
                    <h1 class="text-xl font-bold mb-4">Daftar Sekretariat</h1>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="text-left p-2 border">Nama</th>
                                    <th class="text-left p-2 border">Kategori</th>
                                    <th class="text-left p-2 border">Link</th>
                                    <th class="text-left p-2 border">Status</th>
                                    <th class="text-left p-2 border">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ketuas as $ketua)
                                    <tr>
                                        <td class="p-2 border">{{ $ketua->name }}</td>
                                        <td class="p-2 border">{{ $ketua->category->name ?? '-' }}</td>
                                        <td class="p-2 border">
                                            <a href="{{ $ketua->link }}" class="text-blue-500 underline" target="_blank">Lihat</a>
                                        </td>
                                        <td class="p-2 border">
                                            <span x-text="getStatusText({{ $ketua->status }})" 
                                                  :class="getStatusClass({{ $ketua->status }})"></span>
                                        </td>
                                        <td class="p-2 border">
                                            <button @click="showEditModal = true; currentKetua = {{ $ketua->id }}; 
                                                editKetuaName = '{{ $ketua->name }}'; 
                                                editKetuaLink = '{{ $ketua->link }}'; 
                                                editKetuaCategory = {{ $ketua->category_id ?? 'null' }}; 
                                                editKetuaStatus = {{ $ketua->status ? 1 : 0 }};" 
                                                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2">
                                                Edit
                                            </button>
                                            <button @click="deleteKetua({{ $ketua->id }}, '{{ $ketua->name }}')" 
                                                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- Add Sekretariat Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Tambah Sekretariat Baru</h2>
                <form @submit.stop.prevent="submitAddForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="ketuaName">Nama</label>
                        <input x-model="newKetuaName" type="text" id="ketuaName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="ketuaLink">Link</label>
                        <input x-model="newKetuaLink" type="url" id="ketuaLink" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="ketuaCategory">Kategori</label>
                        <select x-model="newKetuaCategory" id="ketuaCategory" 
                            class="w-full px-3 py-2 border rounded">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="ketuaStatus">Status</label>
                        <select x-model="newKetuaStatus" id="ketuaStatus" 
                            class="w-full px-3 py-2 border rounded" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
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

        <!-- Edit Sekretariat Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Edit Sekretariat</h2>
                <form @submit.stop.prevent="submitEditForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editKetuaName">Nama</label>
                        <input x-model="editKetuaName" type="text" id="editKetuaName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editKetuaLink">Link</label>
                        <input x-model="editKetuaLink" type="url" id="editKetuaLink" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editKetuaCategory">Kategori</label>
                        <select x-model="editKetuaCategory" id="editKetuaCategory" 
                            class="w-full px-3 py-2 border rounded">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editKetuaStatus">Status</label>
                        <select x-model="editKetuaStatus" id="editKetuaStatus" 
                            class="w-full px-3 py-2 border rounded" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
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
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sekretariatData', () => ({
            showAddModal: false,
            showEditModal: false,
            currentKetua: null,
            newKetuaName: '',
            newKetuaLink: '',
            newKetuaCategory: '',
            newKetuaStatus: 1,
            editKetuaName: '',
            editKetuaLink: '',
            editKetuaCategory: null,
            editKetuaStatus: 1,
            
            isLoading: false,

            getStatusText(status) {
                return status ? 'Aktif' : 'Nonaktif';
            },

            getStatusClass(status) {
                return status ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
            },

            resetForm() {
                this.newKetuaName = '';
                this.newKetuaLink = '';
                this.newKetuaCategory = '';
                this.newKetuaStatus = 1;
            },

            async submitAddForm() {
                try {
                    this.isLoading = true;
                    const response = await fetch('/daftarsekretariat', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.newKetuaName,
                            link: this.newKetuaLink,
                            category_id: this.newKetuaCategory || null,
                            status: this.newKetuaStatus
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menambahkan Sekretariat');
                    }
                    
                    Swal.fire("Berhasil!", "Sekretariat baru telah ditambahkan", "success")
                        .then(() => window.location.reload());
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                    console.error('Error:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            
            async submitEditForm() {
                try {
                    this.isLoading = true;
                    const response = await fetch(`/daftarsekretariat/${this.currentKetua}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.editKetuaName,
                            link: this.editKetuaLink,
                            category_id: this.editKetuaCategory || null,
                            status: this.editKetuaStatus
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal memperbarui Sekretariat');
                    }
                    
                    Swal.fire("Berhasil!", "Sekretariat telah diperbarui", "success")
                        .then(() => window.location.reload());
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                    console.error('Error:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            
            async deleteKetua(id, name) {
                try {
                    const result = await Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: `Anda akan menghapus Sekretariat "${name}"`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    });
                    
                    if (result.isConfirmed) {
                        const response = await fetch(`/daftarsekretariat/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menghapus Sekretariat');
                        }
                        
                        Swal.fire("Berhasil!", `Sekretariat "${name}" telah dihapus`, "success")
                            .then(() => window.location.reload());
                    }
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                    console.error('Error:', error);
                }
            }
        }));
    });
    </script>
</body>
</html>