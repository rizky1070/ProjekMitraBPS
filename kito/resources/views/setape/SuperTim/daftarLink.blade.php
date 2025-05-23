<?php
$title = 'Super Tim';
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
    
    <div x-data="superTimData" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-end mb-4">
                        <button @click="showAddModal = true; resetForm()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Tambah Link
                        </button>
                    </div>
                    <h1 class="text-xl font-bold mb-4">Daftar Super TIM</h1>
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
                                @foreach ($offices as $office)
                                    <tr>
                                        <td class="p-2 border">{{ $office->name }}</td>
                                        <td class="p-2 border">{{ $office->category->name ?? '-' }}</td>
                                        <td class="p-2 border">
                                            <a href="{{ $office->link }}" class="text-blue-500 underline" target="_blank">Lihat</a>
                                        </td>
                                        <td class="p-2 border">
                                            <span x-text="getStatusText({{ $office->status }})" 
                                                  :class="getStatusClass({{ $office->status }})"></span>
                                        </td>
                                        <td class="p-2 border">
                                            <button @click="showEditModal = true; currentOffice = {{ $office->id }}; 
                                                editOfficeName = '{{ $office->name }}'; 
                                                editOfficeLink = '{{ $office->link }}'; 
                                                editOfficeCategory = {{ $office->category_id ?? 'null' }}; 
                                                editOfficeStatus = {{ $office->status ? 1 : 0 }};" 
                                                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2">
                                                Edit
                                            </button>
                                            <button @click="deleteOffice({{ $office->id }}, '{{ $office->name }}')" 
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

        <!-- Add Office Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Tambah Super Tim Baru</h2>
                <form @submit.stop.prevent="submitAddForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="officeName">Nama</label>
                        <input x-model="newOfficeName" type="text" id="officeName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="officeLink">Link</label>
                        <input x-model="newOfficeLink" type="url" id="officeLink" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="officeCategory">Kategori</label>
                        <select x-model="newOfficeCategory" id="officeCategory" 
                            class="w-full px-3 py-2 border rounded">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="officeStatus">Status</label>
                        <select x-model="newOfficeStatus" id="officeStatus" 
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

        <!-- Edit Office Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Edit Super Tim</h2>
                <form @submit.stop.prevent="submitEditForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editOfficeName">Nama</label>
                        <input x-model="editOfficeName" type="text" id="editOfficeName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editOfficeLink">Link</label>
                        <input x-model="editOfficeLink" type="url" id="editOfficeLink" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editOfficeCategory">Kategori</label>
                        <select x-model="editOfficeCategory" id="editOfficeCategory" 
                            class="w-full px-3 py-2 border rounded">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editOfficeStatus">Status</label>
                        <select x-model="editOfficeStatus" id="editOfficeStatus" 
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
        Alpine.data('superTimData', () => ({
            sidebarOpen: false, 
            showAddModal: false,
            showEditModal: false,
            currentOffice: null,
            newOfficeName: '',
            newOfficeLink: '',
            newOfficeCategory: '',
            newOfficeStatus: 1,
            editOfficeName: '',
            editOfficeLink: '',
            editOfficeCategory: null,
            editOfficeStatus: 1,
            
            isLoading: false,

            getStatusText(status) {
                return status ? 'Aktif' : 'Nonaktif';
            },

            getStatusClass(status) {
                return status ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
            },

            resetForm() {
                this.newOfficeName = '';
                this.newOfficeLink = '';
                this.newOfficeCategory = '';
                this.newOfficeStatus = 1;
            },

            async submitAddForm() {
                try {
                    this.isLoading = true;
                    const response = await fetch('/daftarsupertim', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.newOfficeName,
                            link: this.newOfficeLink,
                            category_id: this.newOfficeCategory || null,
                            status: this.newOfficeStatus
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menambahkan Super Tim');
                    }
                    
                    Swal.fire("Berhasil!", "Super Tim baru telah ditambahkan", "success")
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
                    const response = await fetch(`/daftarsupertim/${this.currentOffice}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.editOfficeName,
                            link: this.editOfficeLink,
                            category_id: this.editOfficeCategory || null,
                            status: this.editOfficeStatus
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal memperbarui Super Tim');
                    }
                    
                    Swal.fire("Berhasil!", "Super Tim telah diperbarui", "success")
                        .then(() => window.location.reload());
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                    console.error('Error:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            
            async deleteOffice(id, name) {
                try {
                    const result = await Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: `Anda akan menghapus Super Tim "${name}"`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    });
                    
                    if (result.isConfirmed) {
                        const response = await fetch(`/daftarsupertim/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menghapus Super Tim');
                        }
                        
                        Swal.fire("Berhasil!", `Super Tim "${name}" telah dihapus`, "success")
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