<?php

namespace App\Http\Controllers;
use App\Models\Survei;
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
}

