<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instansi;
use Illuminate\Http\RedirectResponse;

class InstansiController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        // Validasi input
        $request->validate([
            'namalengkap' => 'required|string|max:255',
            'namasingkat' => 'required|string|max:255',
        ]);

        // Cek keunikan 'namasingkat'
        if (Instansi::where('namasingkat', $request->namasingkat)->exists()) {
            return redirect()->back()->withErrors(['namasingkat' => 'Nama Singkat sudah ada, silakan gunakan yang lain.']);
        }

        // Buat instansi
        Instansi::create([
            'namalengkap' => $request->namalengkap,
            'namasingkat' => $request->namasingkat,
        ]);


        return redirect()->back()->with(['success' => 'Data Berhasil Disimpan!']);
    }
}
