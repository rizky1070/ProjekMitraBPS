<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PribadiController extends Controller
{

    public function index()
{
    $links = Link::with('categoryUser')
                    ->where('status', 1)
                    ->where('user_id', Auth::id())
                    ->get(); // hanya ambil data dengan status = 1
    return view('Setape.pribadi.index', compact('links'));
}
    public function daftarLink()
    {
        // Ambil link yang hanya dimiliki oleh user yang sedang login
        $links = Link::with('categoryUser')
                     ->where('user_id', Auth::id())
                     ->get();

        return view('Setape.pribadi.daftarLink', compact('links'));
    }
}
