<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use Illuminate\Http\Request;

class DaftarSurveiBpsController extends Controller
{
    public function index()
    {

        // Mengambil semua data survei
        // $surveys = Survei::paginate(10); 
        $surveys = Survei::with('kecamatan')
                        ->withCount('MitraSurvei')
                        ->paginate(10);

        return view('mitrabps.daftarsurveibps', compact('surveys')); // Memanggil view mitrabps.blade.php
    }

    public function addSurvey($id_survei)
    {
        // Mengambil data survei berdasarkan ID
        $survey = Survei::with('kecamatan')
            ->withCount('mitraSurvei') // Sesuaikan relasi jika ada perbedaan nama
            ->where('id_survei', $id_survei) // Gunakan nama kolom yang benar
            ->firstOrFail(); // Gantilah findOrFail dengan firstOrFail

        // Mengambil semua mitra dengan relasi kecamatan
        $mitras = Mitra::with('kecamatan')->get();

        // Bungkus $survey dalam array agar sesuai dengan struktur sebelumnya
        $surveys = [$survey];

        return view('mitrabps.selectSurvey', compact('surveys', 'mitras')); // Pastikan nama view benar
    }


}

