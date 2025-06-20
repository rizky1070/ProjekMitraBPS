<div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    style="display: none;">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Tambah Link Baru</h2>
        <form @submit.stop.prevent="submitAddForm">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="linkName">Nama</label>
                <input x-model="newLinkName" type="text" id="linkName" class="w-full px-3 py-2 border rounded"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="linkLink">Link</label>
                <input x-model="newLinkLink" type="text" id="linkLink" class="w-full px-3 py-2 border rounded"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="linkCategory">Kategori</label>
                <select x-model="newLinkCategory" id="linkCategory" class="w-full border border-gray-500 rounded" x-ref="categorySelect">
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
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