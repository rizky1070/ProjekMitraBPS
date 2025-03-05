<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disposisi;
use Illuminate\Http\RedirectResponse;

class DisposisiController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        // Validasi input
        $request->validate([
            'namadisposisi' => 'required|string|max:255',

        ]);

        // Cek keunikan 'namasingkat'
        if (Disposisi::where('namadisposisi', $request->namadisposisi)->exists()) {
            return redirect()->back()->withErrors(['namadisposisi' => 'Nama Disposisi sudah ada, silakan gunakan yang lain.']);
        }

        // Buat instansi
        Disposisi::create([
            'namadisposisi' => $request->namadisposisi,

        ]);


        return redirect()->back()->with(['success' => 'Data Berhasil Disimpan!']);
    }
}
