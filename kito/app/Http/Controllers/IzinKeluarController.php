<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Izinkeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class IzinKeluarController extends Controller
{
    //
    
    public function histori(Request $request)
    {
        // Set the number of items per page
        $perPage = 10; // Change this number as needed
    
        // Define the query to fetch data
        $query = Izinkeluar::query(); // Ganti dengan model yang sesuai
    
        // Execute the query, order by 'name' and paginate
        $izinkeluars = $query->orderBy('tanggalizin', 'desc')->paginate($perPage);
    
        // Map the results to transform the 'absen' format
        $izinkeluars->getCollection()->transform(function ($izinkeluar) {
            $namapegawai = $izinkeluar->user->name; // Using eager loading for 'user'
            
            return [
                'tanggalizin' => Carbon::parse($izinkeluar->tanggalizin)
                    ->locale('id') // Set locale to Indonesia
                    ->translatedFormat('d F Y'), // Format: 13 Oktober 2024

    
                'jamizin' => Carbon::parse($izinkeluar->jamizin)
                    ->format('H:i'), // Format as time (24-hour clock)
    
                'name' => $namapegawai,
                'keperluan' => $izinkeluar->keperluan,
                'id' => $izinkeluar->id,
                'created_at' => Carbon::parse($izinkeluar->created_at)
                    ->locale('id') // Set locale to Indonesia
                    ->translatedFormat('H:i'), // Format as date and time
            ];
        });
    
        // Return the view with paginated data
        return view('historiizinkeluar', [
            'izinkeluars' => $izinkeluars,
            'pagination' => $izinkeluars // Pass the paginated data correctly to the view
        ]);
    }

    
    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        // Buat instansi
        Izinkeluar::create([
            'user_id' => Auth()->user()->id,
            'tanggalizin' => $request->tanggalizin, // datepicker range start
            'jamizin' => $request->jamizin,
            'keperluan' => $request->keperluan,
            'status' => 1,
        ]);


        return redirect()->back()->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function index()
    {
        $tanggalizininput = Carbon::now()->toDateString();

        $query = Izinkeluar::query();
        if ($tanggalizininput) {
            $tanggalizin =  $tanggalizininput;

            // Filter the query based on the provided startDate and endDate
            $query->where('tanggalizin', '=', $tanggalizin)
                ->where('status', '=', 1);
        }

        $pegawaikeluars = $query->with('user') // Pastikan relasi 'user' ada di model PegawaiKeluar
            ->orderBy('jamizin', 'asc')
            ->get();

        $pegawaikeluars->transform(function ($pegawaikeluar) {
            $namapegawai = $pegawaikeluar->user->name; // Menggunakan eager loading
            return [
                'jamizin' =>  Carbon::parse($pegawaikeluar->jamizin)->format('H:i'),
                'user_id' => $pegawaikeluar->user_id,
                'namapegawai' => $namapegawai,
                'keperluan' => $pegawaikeluar->keperluan,
                'id' => $pegawaikeluar->id,
                'created_at' => Carbon::parse($pegawaikeluar->created_at)->translatedFormat('H:i'),
            ];
        });

        return view('daftarizinkeluar', ['pegawaikeluars' => $pegawaikeluars]);
    }

    public function cekPulang()
    {
        $user_id = Auth()->user()->id;
        // Mengambil data terakhir dari Izinkeluar berdasarkan user_id dan status 1 (izin)
        $izinTerakhir = Izinkeluar::where('user_id', $user_id)
            ->where('status', 1) // status 'izin' dengan kode 1
            ->orderBy('created_at', 'desc') // Atau bisa menggunakan kolom lain yang relevan, seperti 'jamizin' atau 'tanggalizin'
            ->first(); // Ambil yang pertama (terbaru)

        if ($izinTerakhir) {
            $izinid = $izinTerakhir->id;
            // Jika data ditemukan, Anda bisa mengakses kolom-kolomnya, misalnya
            return view('konfirmasiizin', ['userid' => $user_id, 'izinid' => $izinid]);
        } else {
            // Jika tidak ada data, Anda bisa menghandle dengan cara lain
            return view('izinkeluarform');
        }
    }

    public function update(Request $request, $id)
    {
        $izin = Izinkeluar::findOrFail($id);
        $izin->update([
            'status'    => 0,
        ]);
        return redirect()->back()->with(['success' => 'Data Berhasil Diupdate!'], 200);
    }
}
