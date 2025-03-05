<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Email Permintaan Barang</title>
</head>
<body style="background-color: #f7fafc; padding: 2rem;">
    <div style="max-width: 600px; margin: auto; background-color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">      
        <h1 style="font-size: 1.5rem; color: #3b82f6; text-align: center; margin-bottom: 1rem;">Permintaan Barang</h1>
        
        <p style="color: #4a5568;">Halo, <strong>{{ $pesannama }}</strong> mengajukan permintaan.</p>
        <p style="color: #4a5568; margin-top: 0.5rem; padding-left: 1rem;">
            <strong>Barang yang diminta:</strong> {{ $pesanbarang }}
        </p>
        <p style="color: #4a5568; margin-top: 0.5rem; padding-left: 1rem;">
            <strong>Jumlah stok yang diminta:</strong> {{ $pesanstok }}
        </p>
        
        <p style="color: #718096; margin-top: 1rem;">Terima kasih telah menggunakan aplikasi kami!</p>
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ url('kito.link3516.com') }}" style="display: inline-block; padding: 0.5rem 1rem; color: white; background-color: #3b82f6; border-radius: 4px; text-decoration: none;">Lihat Detail</a>
        </div>
    </div>
</body>
</html>