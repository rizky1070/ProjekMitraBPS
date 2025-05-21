<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PribadiController extends Controller
{
    public function daftarLink()
    {
        // Ambil link yang hanya dimiliki oleh user yang sedang login
        $links = Link::with('categoryUser')
                     ->where('user_id', Auth::id())
                     ->get();

        return view('Setape.pribadi.daftarLink', compact('links'));
    }
}
