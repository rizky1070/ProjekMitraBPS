<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Barang;
use App\Models\InputBarang;
use Illuminate\Http\Request;

class InputBarangController extends Controller
{
    //
    public function inputBarangForm($id)
    {

        $barangs = Barang::find($id);

        // Jika event tidak ditemukan
        if (!$barangs) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        $barang = [
            'namabarang' => $barangs->namabarang,
            'stoktersedia' => $barangs->stoktersedia,
            'id' => $barangs->id,
        ];
        // Kirim data ke view
        return view('siminbar.siminbarinputbarangform', ['barang' => $barang]);
    }

    public function store(Request $request){
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }
        $barang = Barang::find($request->id);
        $jml = $barang->stoktersedia;
        $barang->update([
            'stoktersedia' => $jml+$request->jumlahtambah,
        ]);
        InputBarang::create([
            'tanggal' => Carbon::parse($request->inputdate . ' ' . $request->inputtime),
            'jumlahtambah' => $request->jumlahtambah,
            'barang_id' => $request->id,
        ]);
        return redirect('siminbardaftarbarang')->with(['Stok barang telah ditambahkan'],200);
    }
}
