<?php

namespace App\Http\Controllers;

use App\Models\CategoryUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriPribadiController extends Controller
{
    public function index()
    {
        // Ambil data CategoryUser berdasarkan user yang sedang login
        $categoryuser = CategoryUser::where('user_id', Auth::id())->get();
        
        return view('Setape.kategoriPribadi.daftar', compact('categoryuser'));
    }
}
