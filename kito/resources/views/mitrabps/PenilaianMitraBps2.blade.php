<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <div class="flex items-center border-b pb-4">
        <img src="{{ asset('images/profile-placeholder.png') }}" alt="Foto Mitra" class="w-20 h-20 rounded-full">
        <div class="ml-4">
            <h2 class="text-xl font-bold">El Fulano</h2>
        </div>
    </div>

    <div class="mt-4 border-b pb-4">
        <div class="grid grid-cols-2 gap-2 text-sm">
            <span class="font-semibold">Survei / Sensus:</span> <span>-</span>
            <span class="font-semibold">Kecamatan:</span> <span>Kec. Bangsal</span>
            <span class="font-semibold">Lokasi:</span> <span>Desa Dusun</span>
            <span class="font-semibold">Posisi:</span> <span>081********</span>
            <span class="font-semibold">Jadwal:</span> <span>yadayadayada@gmail.com</span>
        </div>
    </div>

    <div class="mt-6 text-center">
        <div class="flex justify-center space-x-2">
            @for ($i = 0; $i < 5; $i++)
                <svg class="w-10 h-10 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 15l-5.5 3 2-6-5-4h6l2-6 2 6h6l-5 4 2 6-5.5-3z"/>
                </svg>
            @endfor
        </div>
        <p class="mt-4 text-lg font-semibold">Catatan:</p>
        <textarea class="w-full mt-2 p-2 border rounded-md" placeholder="Catatan untuk mitra"></textarea>

        <button class="mt-4 bg-orange-500 text-white px-4 py-2 rounded-lg">Tambah</button>
    </div>

    <div class="mt-6 flex justify-center space-x-2">
        @for ($i = 1; $i <= 5; $i++)
            <button class="w-8 h-8 rounded {{ $i == 1 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500' }}">{{ $i }}</button>
        @endfor
    </div>
</div>