<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use App\Models\MitraSurvei;
use Illuminate\Http\Request;

class DaftarSurveiBpsController extends Controller
{
    public function index()
    {
        // Mengambil data survei dengan relasi kecamatan
        $surveys = Survei::with('kecamatan')
                        ->withCount('MitraSurvei')
                        ->paginate(10);

        return view('mitrabps.daftarsurveibps', compact('surveys')); // Memanggil view mitrabps.blade.php
    }

    public function addSurvey($id_survei)
    {
        // Mengambil data survei berdasarkan id_survei
        $survey = Survei::with('kecamatan')
            ->where('id_survei', $id_survei)
            ->firstOrFail();

        // Mengambil semua mitra dan menghitung jumlah relasi MitraSurvei
        $mitras = Mitra::paginate(10); // Bisa juga ditambahkan ->get() jika tanpa pagination

        return view('mitrabps.selectSurvey', compact('survey', 'mitras'));
    }

}

