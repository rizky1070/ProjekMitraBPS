<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = Mitra::paginate(8); // Menampilkan 8 data per halaman
        return view('mitrabps.daftarmitrabps', compact('mitras'));
    }
}
