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
                <h1 class="text-xl font-bold mb-4">Daftar Link Pribadi</h1>
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between mb-4">
                        <div class="flex space-x-4 items-center">
                            <!-- Search Dropdown with Tom Select -->
                            <div class="w-64">
                                <select id="searchSelect" placeholder="Cari link..." class="w-full">
                                    <option value="">Semua Nama</option>
                                    @foreach($linkNames as $name)
                                    <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                             <!-- Category Filter with Tom Select -->
                            <div class="w-48">
                                <select id="categoryFilter" placeholder="Pilih kategori" class="w-full">
                                    <option value="all">Semua Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button @click="showAddModal = true; resetForm()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Tambah Link
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        @foreach ($links as $link)
                            <div class="flex items-center justify-between border-2 border-gray-400 rounded-3xl pl-5 pr-2 m-2">
                                <div>
                                    <a href="{{ $link->link }}" class="text-xl font-bold transition-all duration-200 hover:text-2xl">
                                        {{ $link->name ?? $link->link ?? 'Tidak ada link' }}
                                    </a>
                                    <p>{{ $link->categoryUser->name }}</p>
                                </div>
                                <div class="mr-4">
                                    <button @click="showEditModal = true; currentLink = {{ $link->id }}; 
                                                    editLinkName = '{{ $link->name }}'; 
                                                    editLinkLink = '{{ $link->link }}'; 
                                                    editLinkCategory = {{ $link->category_id ?? 'null' }}; 
                                                    editLinkStatus = {{ $link->status ? 1 : 0 }};" 
                                            class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600 mr-2"
                                            title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteLink({{ $link->id }}, '{{ $link->name }}')" 
                                            class="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                                            title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
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
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tom Select for search dropdown
    const searchSelect = new TomSelect('#searchSelect', {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: "Cari link...",
        maxOptions: null,
    });

    // Initialize Tom Select for category dropdown
    const categorySelect = new TomSelect('#categoryFilter', {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: "Pilih kategori...",
        maxOptions: null,
    });
    
    function applyFilters() {
        const params = new URLSearchParams();
        
        // Add search parameter if exists and not empty
        const searchValue = searchSelect.getValue();
        if (searchValue) {
            params.append('search', searchValue);
        }

        // Add category parameter
        const categoryValue = categorySelect.getValue();
        if (categoryValue && categoryValue !== 'all') {
            params.append('category', categoryValue);
        }

        // Reload page with new query parameters
        window.location.href = window.location.pathname + '?' + params.toString();
    }
    
    // Event listeners
    searchSelect.on('change', applyFilters);
    categorySelect.on('change', applyFilters);
});
</script>
</body>
</html>