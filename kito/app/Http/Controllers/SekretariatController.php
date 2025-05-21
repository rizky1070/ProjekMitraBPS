<?php

namespace App\Http\Controllers;
use App\Models\Ketua;
use Illuminate\Http\Request;

class SekretariatController extends Controller
{
    public function index()
    {
        $ketuas = ketua::with('category')->get(); // assuming you have relation with category
        return view('Setape.sekretariat.index', compact('ketuas'));
    }
}
