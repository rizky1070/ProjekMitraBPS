<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    //
    // Direktori tempat gambar disimpan
    const UPLOAD_DIR = 'uploads/';
    // Ekstensi gambar yang diizinkan
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];
    // Ukuran maksimum file (dalam bytes)
    const MAX_FILE_SIZE = 5000000; // 5000 KB

    /**
     * Fungsi untuk meng-upload gambar dan menyimpan path ke database
     */
    public function uploadImage(Request $request)
    {
        // Validasi bahwa file gambar telah dikirim
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());

            // Validasi ukuran file dan tipe file
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                return back()->with('error', 'Ukuran file terlalu besar.');
            }

            if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
                return back()->with('error', 'Hanya file JPG, JPEG, PNG & GIF yang diizinkan.');
            }

            // Simpan file ke folder 'uploads/' dan ambil path-nya
            $path = $file->store(self::UPLOAD_DIR, 'public');

            // Simpan path ke database
            DB::table('images')->insert([
                'image_path' => $path,
                // 'schedule_id' => $path->schedule_id, // intinya ini biar dia nyangkut ke event nya. apa gausa ya> kan dah ada image_path. jadi nanti yang butuh fk tuh si event nya buat ambil image path nya
                'created_at' => now()
            ]);

            return back()->with('success', 'Gambar berhasil di-upload dan disimpan.');
        } else {
            return back()->with('error', 'Tidak ada file yang di-upload.');
        }
    }

    /**
     * Fungsi untuk menampilkan semua gambar yang di-upload
     */
    public function showImages()
    {
        // Ambil semua path gambar dari database
        $images = DB::table('images')->get();

        // Kirim data gambar ke view
        return view('images.index', ['images' => $images]);
    }

    public function getImages(){
        $images = image::all();
        return response()->json($images);
    }
}
