<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class KategoriUmumController extends Controller
{
    public function index()
    {
        $category = Category::get(); // assuming you have relation with category
        return view('Setape.kategoriUmum.daftarKategoriUmum', compact('category'));
    }
}
