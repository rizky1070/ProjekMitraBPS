<?php
$title = 'Kelola Kategori Umum';
?>
@include('mitrabps.headerTemp')
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="icon" href="/Logo BPS.png" type="image/png">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
</head>
<body class="h-full">
    
    <div x-data="categoryData" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="flex justify-between mb-4">
                    <h1 class="text-2xl font-bold mb-4">Kelola Kategori Umum</h1>
                    <a href="/kategoripribadi" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Kategori Pribadi
                    </a>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between mb-4">
                        <div class="flex space-x-4 items-center">
                            <div class="w-64">
                                <select id="searchSelect" placeholder="Cari kategori..." class="w-full">
                                    <option value="">Semua Nama</option>
                                    @foreach($kategoriNames as $name)
                                    <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button @click="showAddModal = true; newCategoryName = ''" 
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Tambah Kategori
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        @foreach ($categories as $category)
                        <div class="flex items-center justify-between border-2 border-gray-400 rounded-full py-1 pl-5 pr-2 m-2">
                            <div>
                                <span class="text-lg font-semibold">{{ $category->name }}</span>
                            </div>
                            <div class="my-1">
                                <button @click="showEditModal = true; currentCategory = {{ $category->id }}; editCategoryName = '{{ $category->name }}'" 
                                    class="bg-yellow-500 text-white mr-2 px-2 py-2 rounded-full rounded hover:bg-yellow-600" 
                                    title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button @click="deleteCategory({{ $category->id }}, '{{ $category->name }}')" 
                                    class="bg-red-500 text-white px-2 py-2 rounded-full rounded hover:bg-red-600" 
                                    title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
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

        <!-- Add Category Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Tambah Kategori Baru</h2>
                <form @submit.stop.prevent="submitAddForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="newCategoryName">Nama Kategori</label>
                        <input x-model="newCategoryName" type="text" id="newCategoryName" 
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

        <!-- Edit Category Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Edit Kategori</h2>
                <form @submit.stop.prevent="submitEditForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editCategoryName">Nama Kategori</label>
                        <input x-model="editCategoryName" type="text" id="editCategoryName" 
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
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('categoryData', () => ({
            sidebarOpen: false, 
            showAddModal: false,
            showEditModal: false,
            currentCategory: null,
            newCategoryName: '',
            editCategoryName: '',
            
            isLoading: false,

            async submitAddForm() {
                try {
                    const response = await fetch('/kategoriumum', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.newCategoryName
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menambahkan kategori');
                    }
                    
                    Swal.fire("Berhasil!", "Kategori baru telah ditambahkan", "success")
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
                    const response = await fetch(`/kategoriumum/${this.currentCategory}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.editCategoryName
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal memperbarui kategori');
                    }
                    
                    Swal.fire("Berhasil!", "Kategori telah diperbarui", "success")
                        .then(() => window.location.reload());
                } catch (error) {
                    Swal.fire("Error!", error.message, "error");
                    console.error('Error:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            
            async deleteCategory(id, name) {
                try {
                    const result = await Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: `Anda akan menghapus kategori "${name}"`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    });
                    
                    if (result.isConfirmed) {
                        const response = await fetch(`/kategoriumum/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menghapus kategori');
                        }
                        
                        Swal.fire("Berhasil!", `Kategori "${name}" telah dihapus`, "success")
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
// Di bagian JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tom Select for search dropdown
    const searchSelect = new TomSelect('#searchSelect', {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: "Cari kategori...",
        maxOptions: null,
    });
    
    
    function applyFilters() {
        const params = new URLSearchParams();
        
        // Add search parameter
        const searchValue = searchSelect.getValue();
        if (searchValue) {
            params.append('search', searchValue);
        }
        
        
        // Reload page with new query parameters
        window.location.href = window.location.pathname + '?' + params.toString();
    }
    
    // Event listeners
    searchSelect.on('change', applyFilters);
});
</script>
</body>
</html>