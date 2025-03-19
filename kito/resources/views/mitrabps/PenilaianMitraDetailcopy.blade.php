<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <div class="flex items-center border-b pb-4">
        <img src="{{ asset('images/profile-placeholder.png') }}" alt="Foto Mitra" class="w-20 h-20 rounded-full">
        <div class="ml-4">
            <h2 class="text-xl font-bold">{{ $mitra->nama }}</h2>
        </div>
    </div>

    <div class="mt-4 border-b pb-4">
        <div class="grid grid-cols-2 gap-2 text-sm">
            <span class="font-semibold">Survei / Sensus:</span> <span>{{ $mitra->surveis->first()->nama ?? '-' }}</span>
            <span class="font-semibold">Kecamatan:</span> <span>{{ $mitra->kecamatan->nama ?? '-' }}</span>
            <span class="font-semibold">Lokasi:</span> <span>{{ $mitra->lokasi ?? '-' }}</span>
            <span class="font-semibold">Posisi:</span> <span>{{ substr($mitra->telepon, 0, 3) . str_repeat('*', 6) . substr($mitra->telepon, -2) }}</span>
            <span class="font-semibold">Jadwal:</span> <span>{{ $mitra->email }}</span>
        </div>
    </div>

    <form action="{{ route('penilaian.mitra.store', $mitra->id) }}" method="POST">
        @csrf
        <div class="mt-6 text-center">
            <p class="text-lg font-semibold">Penilaian:</p>
            <div class="flex justify-center space-x-2">
                @for ($i = 1; $i <= 5; $i++)
                    <label>
                        <input type="radio" name="nilai" value="{{ $i }}" class="hidden">
                        <svg class="w-10 h-10 cursor-pointer text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 15l-5.5 3 2-6-5-4h6l2-6 2 6h6l-5 4 2 6-5.5-3z"/>
                        </svg>
                    </label>
                @endfor
            </div>

            <p class="mt-4 text-lg font-semibold">Catatan:</p>
            <textarea name="catatan" class="w-full mt-2 p-2 border rounded-md" placeholder="Catatan untuk mitra"></textarea>

            <button type="submit" class="mt-4 bg-orange-500 text-white px-4 py-2 rounded-lg">Tambah</button>
        </div>
    </form>
</div>

