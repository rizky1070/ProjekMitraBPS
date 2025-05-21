<?php

namespace App\Http\Controllers;
use App\Models\Office;
use Illuminate\Http\Request;

class SuperTimController extends Controller
{
    public function index()
{
    $offices = Office::with('category')
                    ->where('status', 1)
                    ->get(); // hanya ambil data dengan status = 1
    return view('Setape.superTim.index', compact('offices'));
}

    public function daftarLink()
    {
        $offices = Office::with('category')->get(); // assuming you have relation with category
        return view('Setape.superTim.daftarLink', compact('offices'));
    }
}
