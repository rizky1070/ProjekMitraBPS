<?php
$title = 'Super Tim';
?>
@include('mitrabps.headerTemp')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <!-- component -->
    <div x-data="{
    sidebarOpen: false,
    showAddModal: false,
    showEditModal: false,
    currentCategory: null,
    newCategoryName: '',
    editCategoryName: '',
    
    async submitAddForm() {
        try {
            const response = await fetch('/kategoriumum', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name: this.newCategoryName })
            });
            
            const data = await response.json();
            
            if (!response.ok) throw new Error(data.message || 'Error adding category');
            
            await Swal.fire('Success!', 'Category added successfully', 'success');
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            await Swal.fire('Error!', error.message, 'error');
        }
    },
    
    async submitEditForm() {
        try {
            const response = await fetch(`/kategoriumum/${this.currentCategory}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name: this.editCategoryName })
            });
            
            const data = await response.json();
            
            if (!response.ok) throw new Error(data.message || 'Error updating category');
            
            await Swal.fire('Success!', 'Category updated successfully', 'success');
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            await Swal.fire('Error!', error.message, 'error');
        }
    },
    async deleteCategory(id, name) {
    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: `You will delete ${name}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        });
        
        if (result.isConfirmed) {
            const response = await fetch(`/kategoriumum/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) throw new Error(data.message || 'Error deleting category');
            
            await Swal.fire('Deleted!', 'Category has been deleted.', 'success');
            window.location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire('Error!', error.message, 'error');
    }
}
}" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-end mb-4">
                        <button @click="showAddModal = true; newCategoryName = ''" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Tambah Kategori
                        </button>
                    </div>
                    <h1 class="text-xl font-bold mb-4">Kategori Umum</h1>
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="text-left p-2 border">Nama</th>
                                <th class="text-left p-2 border">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($category as $cat)
                                <tr>
                                    <td class="p-2 border">{{ $cat->name }}</td>
                                    <td class="p-2 border">
                                        <button @click="showEditModal = true; currentCategory = {{ $cat->id }}; editCategoryName = '{{ $cat->name }}'" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2">
                                            Edit
                                        </button>
                                        <button @click="await deleteCategory({{ $cat->id }}, '{{ $cat->name }}')" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </main>
        </div>

        <!-- Add Category Modal -->
        <div x-show="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Tambah Kategori Baru</h2>
                <form @submit.prevent="submitAddForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="newCategoryName">Nama Kategori</label>
                        <input x-model="newCategoryName" type="text" id="newCategoryName" class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">
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
        <div x-show="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Edit Kategori</h2>
                <form @submit.prevent="submitEditForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="editCategoryName">Nama Kategori</label>
                        <input x-model="editCategoryName" type="text" id="editCategoryName" class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">
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
        
        // Fungsi untuk menambah kategori
        submitAddForm() {
            fetch('/kategoriumum', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: this.newCategoryName
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire("Berhasil!", "Kategori baru telah ditambahkan", "success")
                    .then(() => window.location.reload());
                } else {
                    throw new Error(data.message || 'Gagal menambahkan kategori');
                }
            })
            .catch(error => {
                Swal.fire("Error!", error.message, "error");
                console.error('Error:', error);
            });
        },
        
        // Fungsi untuk mengedit kategori
        submitEditForm() {
            fetch(`/kategoriumum/${this.currentCategory}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: this.editCategoryName
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire("Berhasil!", "Kategori telah diperbarui", "success")
                    .then(() => window.location.reload());
                } else {
                    throw new Error(data.message || 'Gagal memperbarui kategori');
                }
            })
            .catch(error => {
                Swal.fire("Error!", error.message, "error");
                console.error('Error:', error);
            });
        },
        
        // Fungsi untuk menghapus kategori
        deleteCategory(id, name) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: `Anda akan menghapus kategori "${name}"`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/kategoriumum/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Berhasil!", `Kategori "${name}" telah dihapus`, "success")
                            .then(() => window.location.reload());
                        } else {
                            throw new Error(data.message || 'Gagal menghapus kategori');
                        }
                    })
                    .catch(error => {
                        Swal.fire("Error!", error.message, "error");
                        console.error('Error:', error);
                    });
                }
            });
        }
    }));
});
</script>
</body>
</html>