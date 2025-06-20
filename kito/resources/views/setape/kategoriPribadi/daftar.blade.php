<?php
$title = 'Kelola Kategori Pribadi';
?>
@include('mitrabps.headerTemp')
</head>
<body class="h-full">
    
    <div x-data="categoryData" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden cuScrollGlobalY bg-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold mb-4">Kelola Kategori Pribadi</h1>
                    @if (auth()->user()->is_admin || auth()->user()->is_leader)
                    <a href="/kategoriumum" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Kategori Umum
                    </a>
                    @endif
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex flex-col md:flex-row md:justify-between mb-4 space-y-2 md:space-y-0 md:space-x-4">
                        <div class="flex flex-col md:flex-row md:space-x-4 items-start md:items-center">
                            <div class="w-full md:w-64">
                                <select id="searchSelect" placeholder="Cari nama..." class="w-full">
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
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition w-full md:w-auto">
                            Tambah Kategori
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <div class="grid grid-cols-1 gap-1 md:grid-cols-2 md:gap-4">
                        @foreach ($categoryuser as $category)
                        <div class="flex items-center justify-between border-2 border-gray-400 rounded-full pl-3 pr-1 py-1 transition-all duration-200 hover:shadow-lg hover:border-blue-500 bg-white"> 
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="min-w-0 flex-1">
                                    <span class="text-lg font-semibold block truncate" title="{{ $category->name }}">
                                        {{ $category->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 flex space-x-1">
                                <button @click="showEditModal = true; currentCategory = {{ $category->id }}; editCategoryName = '{{ $category->name }}'" 
                                    class="bg-yellow-500 text-white p-1 rounded-full hover:bg-yellow-600 transition-colors duration-200"
                                    title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button @click="deleteCategory({{ $category->id }}, '{{ $category->name }}')" 
                                    class="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors duration-200"
                                    title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                        </div>
                    </div>
                </div>
                @include('setape.setapePage', ['paginator' => $categoryuser])
            </main>
        </div>

        <!-- Add Category Modal -->
        @include('setape.kategoriPribadi.create')

        <!-- Edit Category Modal -->
        @include('setape.kategoriPribadi.update')
        
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
                    const response = await fetch('/kategoripribadi', {
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
                    const response = await fetch(`/kategoripribadi/${this.currentCategory}`, {
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
                        const response = await fetch(`/kategoripribadi/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (response.status === 422) {
                            // Jika error karena kategori memiliki link
                            await Swal.fire({
                                title: "Gagal Menghapus",
                                text: data.message,
                                icon: "error",
                                confirmButtonText: "Mengerti"
                            });
                            return;
                        }
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menghapus kategori');
                        }
                        
                        await Swal.fire({
                            title: "Berhasil!",
                            text: `Kategori "${name}" telah dihapus`,
                            icon: "success",
                            confirmButtonText: "OK"
                        });
                        window.location.reload();
                    }
                } catch (error) {
                    await Swal.fire({
                        title: "Error!",
                        text: error.message.includes('links()') 
                            ? 'Gagal menghapus kategori karena kategori sedang digunakan' 
                            : error.message,
                        icon: "error",
                        confirmButtonText: "Mengerti"
                    });
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