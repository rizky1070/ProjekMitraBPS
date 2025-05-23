<?php
$title = 'Daftar Link Pribadi';
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
    
    <div x-data="linkData" class="flex h-screen">
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
                    <h1 class="text-xl font-bold mb-4">Daftar Link Pribadi</h1>
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
                                @foreach ($links as $link)
                                    <tr>
                                        <td class="p-2 border">{{ $link->name }}</td>
                                        <td class="p-2 border">{{ $link->categoryUser->name ?? '-' }}</td>
                                        <td class="p-2 border">
                                            <a href="{{ $link->link }}" class="text-blue-500 underline" target="_blank">Lihat</a>
                                        </td>
                                        <td class="p-2 border">
                                            <span x-text="getStatusText({{ $link->status }})" 
                                                  :class="getStatusClass({{ $link->status }})"></span>
                                        </td>
                                        <td class="p-2 border">
                                            <button @click="showEditModal = true; currentLink = {{ $link->id }}; 
                                                editLinkName = '{{ $link->name }}'; 
                                                editLinkLink = '{{ $link->link }}'; 
                                                editLinkCategory = {{ $link->category_user_id ?? 'null' }}; 
                                                editLinkStatus = {{ $link->status ? 1 : 0 }};" 
                                                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2">
                                                Edit
                                            </button>
                                            <button @click="deleteLink({{ $link->id }}, '{{ $link->name }}')" 
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

        <!-- Add Link Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Tambah Link Baru</h2>
                <form @submit.stop.prevent="submitAddForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="linkName">Nama</label>
                        <input x-model="newLinkName" type="text" id="linkName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="linkLink">Link</label>
                        <input x-model="newLinkLink" type="url" id="linkLink" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="linkCategory">Kategori</label>
                        <select x-model="newLinkCategory" id="linkCategory" 
                            class="w-full px-3 py-2 border rounded">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="linkStatus">Status</label>
                        <select x-model="newLinkStatus" id="linkStatus" 
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

        <!-- Edit Link Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Edit Link</h2>
                <form @submit.stop.prevent="submitEditForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editLinkName">Nama</label>
                        <input x-model="editLinkName" type="text" id="editLinkName" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editLinkLink">Link</label>
                        <input x-model="editLinkLink" type="url" id="editLinkLink" 
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editLinkCategory">Kategori</label>
                        <select x-model="editLinkCategory" id="editLinkCategory" 
                            class="w-full px-3 py-2 border rounded">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editLinkStatus">Status</label>
                        <select x-model="editLinkStatus" id="editLinkStatus" 
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
        Alpine.data('linkData', () => ({
            sidebarOpen: false, 
            showAddModal: false,
            showEditModal: false,
            currentLink: null,
            newLinkName: '',
            newLinkLink: '',
            newLinkCategory: '',
            newLinkStatus: 1,
            editLinkName: '',
            editLinkLink: '',
            editLinkCategory: null,
            editLinkStatus: 1,
            
            isLoading: false,

            getStatusText(status) {
                return status ? 'Aktif' : 'Nonaktif';
            },

            getStatusClass(status) {
                return status ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
            },

            resetForm() {
                this.newLinkName = '';
                this.newLinkLink = '';
                this.newLinkCategory = '';
                this.newLinkStatus = 1;
            },

            async submitAddForm() {
                try {
                    this.isLoading = true;
                    const response = await fetch('/daftarlinkpribadi', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.newLinkName,
                            link: this.newLinkLink,
                            category_user_id: this.newLinkCategory || null,
                            status: this.newLinkStatus
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menambahkan Link');
                    }
                    
                    Swal.fire("Berhasil!", "Link baru telah ditambahkan", "success")
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
                    const response = await fetch(`/daftarlinkpribadi/${this.currentLink}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.editLinkName,
                            link: this.editLinkLink,
                            category_user_id: this.editLinkCategory || null,
                            status: this.editLinkStatus
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal memperbarui Link');
                    }
                    
                    Swal.fire("Berhasil!", "Link telah diperbarui", "success")
                        .then(() => window.location.reload());
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                    console.error('Error:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            
            async deleteLink(id, name) {
                try {
                    const result = await Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: `Anda akan menghapus Link "${name}"`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    });
                    
                    if (result.isConfirmed) {
                        const response = await fetch(`/daftarlinkpribadi/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menghapus Link');
                        }
                        
                        Swal.fire("Berhasil!", `Link "${name}" telah dihapus`, "success")
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