<?php
$title = 'Super Tim';
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
    Swal.fire("Success!", "{{ session('success') }}", "success");
    </script>
    @endif

    @if ($errors->any())
    <script>
    Swal.fire("Error!", "{{ $errors->first() }}", "error");
    </script>
    @endif
    
    @if (session('error'))
    <script>
    Swal.fire("Error!", "{{ session('error') }}", "error");
    </script>
    @endif
    
    <div x-data="superTimData" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold mb-4">Kelola Link Super Tim</h1>
                    <a href="/daftarsekretariat" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Sekretariat 
                    </a>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="my-1 flex flex-col md:flex-row md:justify-between mb-4">
                        <div class="my-1 flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 items-stretch md:items-center">
                            <div class="w-full md:w-64">
                                <select id="searchSelect" placeholder="Cari link..." class="w-full">
                                    <option value="">Semua Nama</option>
                                    @foreach($officeNames as $name)
                                        <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="w-full md:w-48">
                                <select id="categoryFilter" placeholder="Pilih kategori" class="w-full">
                                    <option value="all">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Status Filter -->
                            <div class="w-full md:w-48">
                                <select id="statusFilter" placeholder="Pilih status" class="w-full">
                                    <option value="all">Semua Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <button @click="showAddModal = true; resetForm()" 
                            class="my-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Tambah Link
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        @if ($offices->isEmpty())
                            <div class="text-center text-gray-500 py-8 text-2xl font-bold flex flex-col items-center">
                                Tidak ada link
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mt-2" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" fill="#F3F4F6"/>
                                    <circle cx="9" cy="10" r="1.5" fill="#6B7280"/>
                                    <circle cx="15" cy="10" r="1.5" fill="#6B7280"/>
                                    <path d="M9 16c.5-1 1.5-1.5 3-1.5s2.5.5 3 1.5" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-1 md:grid-cols-2 md:gap-4">
                            @foreach ($offices as $office)
                                <div class="flex items-center justify-between border-2 border-gray-400 rounded-full px-3 py-1 transition-all duration-200 hover:shadow-lg hover:border-blue-500 bg-white"> 
                                    <div class="flex items-center flex-1 min-w-0">
                                        <button 
                                            @click="togglePin({{ $office->id }})" 
                                            class="flex-shrink-0 flex items-center justify-center p-1 rounded-full mr-2 transition-colors duration-200 {{ $office->priority ? 'bg-red-500 text-white' : 'bg-gray-300 text-gray-600' }}"
                                            title="{{ $office->priority ? 'Lepaskan Pin' : 'Sematkan Pin' }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ $office->link }}" class="text-xl font-bold block truncate" title="{{ $office->name }}">
                                                {{ $office->name }}
                                            </a>
                                            <p class="truncate">{{ $office->category->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 items-center justify-center flex space-x-1">
                                        <button @click="showEditModal = true; currentOffice = {{ $office->id }}; 
                                                        editOfficeName = '{{ $office->name }}'; 
                                                        editOfficeLink = '{{ $office->link }}'; 
                                                        editOfficeCategory = {{ $office->category_id ?? 'null' }}; 
                                                        editOfficeStatus = {{ $office->status ? 1 : 0 }};" 
                                                class="bg-yellow-500 text-white p-1 rounded-full hover:bg-yellow-600 transition-colors duration-200"
                                                title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                        </button>
                                        <button @click="deleteOffice({{ $office->id }}, '{{ $office->name }}')" 
                                            class="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors duration-200"
                                            title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <label class="switch" title="{{ $office->status ? 'Sembunyikan' : 'Tampilkan' }}">
                                            <input type="checkbox" 
                                                    {{ $office->status ? 'checked' : '' }} 
                                                    data-office-id="{{ $office->id }}"
                                                    class="status-toggle"
                                                    @change="toggleStatus($event)">
                                            <span class="slider {{ $office->status ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @include('setape.setapePage', ['paginator' => $offices])
            </main>
        </div>

        <!-- Add Office Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
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
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
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

            toggleStatus(event) {
                const checkbox = event.target;
                const officeId = checkbox.dataset.officeId;
                const isActive = checkbox.checked;
                const slider = checkbox.nextElementSibling;
                
                // Kirim request AJAX
                fetch('{{ route("super-tim.update-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        office_id: officeId,
                        status: isActive ? 'active' : 'inactive'
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
                        // Revert changes if failed
                        checkbox.checked = !isActive;
                        slider.classList.toggle('bg-blue-600', !isActive);
                        slider.classList.toggle('bg-gray-400', isActive);
                        Swal.fire("Error!", data.message, "error");
                    } else {
                        // Update UI based on new status
                        slider.classList.toggle('bg-blue-600', isActive);
                        slider.classList.toggle('bg-gray-400', !isActive);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    checkbox.checked = !isActive;
                    slider.classList.toggle('bg-blue-600', !isActive);
                    slider.classList.toggle('bg-gray-400', isActive);
                    Swal.fire("Error!", "Terjadi kesalahan jaringan", "error");
                });
            },

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
                            throw new Error(data.message || 'Gagal menambahkan Sekretariat');
                        }
                        
                        Swal.fire("Berhasil!", "Link Super Tim baru telah ditambahkan", "success")
                            .then(() => window.location.reload());
                    } catch (error) {
                        Swal.fire("Error!", error.message, "error");
                        console.error('Error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

            async togglePin(id) {
                try {
                    this.isLoading = true;
                    const response = await fetch(`/daftarsupertim/${id}/toggle-pin`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                        
                    const data = await response.json();
                        
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal mengubah status pin');
                    }
                        
                    Swal.fire("Berhasil!", data.message, "success")
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