<div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    style="display: none;">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Sekretariat</h2>
        <form @submit.stop.prevent="submitEditForm">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="editKetuaName">Nama</label>
                <input x-model="editKetuaName" type="text" id="editKetuaName" class="w-full px-3 py-2 border rounded"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="editKetuaLink">Link</label>
                <input x-model="editKetuaLink" type="text" id="editKetuaLink" class="w-full px-3 py-2 border rounded"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="editKetuaCategory">Kategori</label>
                <select x-model="editKetuaCategory" id="editKetuaCategory" class="w-full border border-gray-500 rounded" x-ref="editCategorySelect">
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
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
