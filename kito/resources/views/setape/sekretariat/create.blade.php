<div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    style="display: none;">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Tambah Sekretariat Baru</h2>
        <form @submit.stop.prevent="submitAddForm">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="ketuaName">Nama</label>
                <input x-model="newKetuaName" type="text" id="ketuaName" class="w-full px-3 py-2 border rounded"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="ketuaLink">Link</label>
                <input x-model="newKetuaLink" type="text" id="ketuaLink" class="w-full px-3 py-2 border rounded"
                    required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="ketuaCategory">Kategori</label>
                <select x-model="newKetuaCategory" id="ketuaCategory" class="w-full" x-ref="categorySelect">
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="ketuaStatus">Status</label>
                <select x-model="newKetuaStatus" id="ketuaStatus" class="w-full px-3 py-2 border rounded" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
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