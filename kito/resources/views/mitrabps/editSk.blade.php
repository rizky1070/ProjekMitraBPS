<!-- resources/views/upload.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload and Edit Document</title>
</head>
<body>

    <h1>Upload and Edit Your Document</h1>

    <!-- Form untuk upload file dan mengedit template -->
    <form action="{{ route('editSk', ['id_survei' => $survei->id_survei, 'id_mitra' => $mitra->id_mitra]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Input untuk Nomor SK -->
        <label for="nomor_sk">Nomor SK:</label>
        <input type="text" name="nomor_sk" required>
        <br><br>

        <!-- Input untuk Nama -->
        <label for="nama">Your Name:</label>
        <input type="text" name="nama" required>
        <br><br>

        <!-- Input untuk Denda -->
        <label for="denda">Denda:</label>
        <input type="number" name="denda" required>
        <br><br>

        <!-- Data dari database (misalnya mitra dan survei) disertakan di dalam input tersembunyi -->
        <input type="hidden" name="id_survei" value="{{ $survei->id_survei }}">
        <input type="hidden" name="id_mitra" value="{{ $mitra->id_mitra }}">

        <label for="file">Choose a DOCX file:</label>
        <input type="file" name="file" required>
        <br><br>

        <button type="submit">Upload and Edit</button>
    </form>


</body>
</html>
