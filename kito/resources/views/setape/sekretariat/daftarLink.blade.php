<?php
$title = 'Sekretariat';
?>
@include('mitrabps.headerTemp')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    @include('setape.switch')
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
                <div class="flex justify-between mb-4">
                    <h1 class="text-2xl font-bold mb-4">Kelola Link Sekretariat</h1>
                    <a href="/daftarsupertim" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Super Tim
                    </a>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between mb-4">
                        <div class="flex space-x-4 items-center">
                            <div class="w-64">
                                <select id="searchSelect" placeholder="Cari link..." class="w-full">
                                    <option value="">Semua Nama</option>
                                    @foreach($ketuaNames as $name)
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
                            <!-- Di bagian filter, tambahkan ini setelah category filter -->
                            <div class="w-48">
                                <select id="statusFilter" placeholder="Pilih status" class="w-full">
                                    <option value="all">Semua Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <button @click="showAddModal = true; resetForm()" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Tambah Link Sekretariat
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        @foreach ($ketuas as $ketua)
                            <div class="flex items-center justify-between border-2 border-gray-400 rounded-3xl pl-5 pr-2 m-2">
                                <div>
                                    <a href="{{ $ketua->link }}" class="text-xl font-bold transition-all duration-200 hover:text-2xl">
                                        {{ $ketua->name ?? $ketua->link ?? 'Tidak ada link' }}
                                    </a>
                                    <p>{{ $ketua->category->name }}</p>
                                </div>
                                <div>
                                    <button @click="showEditModal = true; currentKetua = {{ $ketua->id }}; 
                                                    editKetuaName = '{{ $ketua->name }}'; 
                                                    editKetuaLink = '{{ $ketua->link }}'; 
                                                    editKetuaCategory = {{ $ketua->category_id ?? 'null' }}; 
                                                    editKetuaStatus = {{ $ketua->status ? 1 : 0 }};" 
                                            class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600 mr-2"
                                            title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteKetua({{ $ketua->id }}, '{{ $ketua->name }}')" 
                                            class="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                                            title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <label class="switch">
                                        <input type="checkbox" 
                                                {{ $ketua->status ? 'checked' : '' }} 
                                                data-ketua-id="{{ $ketua->id }}"
                                                class="status-toggle">
                                        <span class="slider {{ $ketua->status ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status toggle functionality
            document.querySelectorAll('.status-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const ketuaId = this.getAttribute('data-ketua-id');
                    const isActive = this.checked;
                    const slider = this.nextElementSibling;
                    
                    // Simpan status awal untuk fallback
                    const originalStatus = !isActive;
                    
                    // Update UI immediately
                    slider.classList.toggle('bg-blue-600', isActive);
                    slider.classList.toggle('bg-gray-400', !isActive);
                    
                    // Send AJAX request
                    fetch('{{ route("sekretariat.update-status") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            ketua_id: ketuaId,
                            status: isActive
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Update failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert changes
                        this.checked = originalStatus;
                        slider.classList.toggle('bg-blue-600', originalStatus);
                        slider.classList.toggle('bg-gray-400', !originalStatus);
                        
                        // Show error message
                        swal("Error!", error.message || "Gagal memperbarui status", "error");
                    });
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sekretariatData', () => ({
                sidebarOpen: false, 
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
            
            // Initialize Tom Select for status dropdown
            const statusSelect = new TomSelect('#statusFilter', {
                create: false,
                placeholder: "Pilih status...",
            });
            
            function applyFilters() {
                const params = new URLSearchParams();
                
                // Add search parameter
                const searchValue = searchSelect.getValue();
                if (searchValue) {
                    params.append('search', searchValue);
                }
                
                // Add category parameter
                const categoryValue = categorySelect.getValue();
                if (categoryValue && categoryValue !== 'all') {
                    params.append('category', categoryValue);
                }
                
                // Add status parameter
                const statusValue = statusSelect.getValue();
                if (statusValue && statusValue !== 'all') {
                    params.append('status', statusValue);
                }
                
                // Reload page with new query parameters
                window.location.href = window.location.pathname + '?' + params.toString();
            }
            
            // Event listeners
            searchSelect.on('change', applyFilters);
            categorySelect.on('change', applyFilters);
            statusSelect.on('change', applyFilters);
        });
    </script>
</body>
</html>