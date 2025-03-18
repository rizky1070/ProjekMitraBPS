<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Profil Mitra</title>
</head>
<body>

    <div class="max-w-4xl mx-auto p-4">
        @foreach ($mits as $mit)
        <div class="flex items-center mb-4">
            <img alt="Profile picture" class="w-24 h-24 rounded-full mr-4" src="{{ $mit->profile_picture }}" width="100" height="100">
            <div>
                <h1 class="text-2xl font-bold">Profil Mitra</h1>
                <h2 class="text-xl">{{ $mit->nama_lengkap }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p><strong>Domisili : {{ $mit->kecamatan->nama_kecamatan }}</strong></p>
                <p><strong>Alamat Detail : {{ $mit->alamat_mitra }}, {{ $mit->desa->nama_desa }}</strong></p>
            </div>
        </div>
        @endforeach
    </div>

</body>
</html>
