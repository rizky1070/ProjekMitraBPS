<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Profil Mitra</h1>
    @foreach($mits as $mit)
        
        <h4>Nama: {{ $mit->nama_lengkap }}</h4>
        <h4>Domisili: {{ $mit->kecamatan->nama_kecamatan }}</h4>
        <h4>Alamat: {{ $mit->alamat_mitra }}, {{ $mit->desa->nama_desa }}</h4>
       
    @endforeach
</body>
</html>