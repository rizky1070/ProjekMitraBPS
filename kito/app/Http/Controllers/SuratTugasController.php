<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SuratTugas;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SuratTugasController extends Controller
{
    //
    public function index(Request $request)
    {
        // Set the number of items per page
        $perPage = 10; // Change this number as needed

        // Retrieve the start and end dates from the request
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $searchKeyword = $request->input('search'); // Ambil kata kunci pencarian

        // Initialize the query berubah
        $query = SuratTugas::with('user'); 
        

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('tanggalsurat', '>=', $startDate)
                ->where('tanggalsurat', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('tujuan', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('fungsi', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }
        
        // Execute the query, paginate, and retrieve the results
        $surattugass = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        

        // Map the results to transform the 'absen' format
        $surattugass->getCollection()->transform(function ($surattugas) {
            return [ // yg ada di tampilan tabel
                'tujuan' => $surattugas->tujuan,
                'tanggalsurat' => Carbon::parse($surattugas->created_at)->translatedFormat('d F Y H:i:s'),
                'nomorfull' => $surattugas->nomorfull,
                'id' => $surattugas->id,
                'user' => $surattugas->user->pluck('name')->implode(', ') // Mengambil nama user berubah
            ];
        });

        // Return the view with paginated presences
        return view('hamukti.hamuktisurattugas', [
            'surattugass' => $surattugass,
            'pagination' => $surattugass // Pass the paginated data correctly to the view
        ]);
    }
    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        $tanggal = $request->input('tanggalsurat');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $kodeangka = $request->input('kodeangka');
        $nomorfull = $request->nosurat . '/' . $kodeangka . '/' . $request->fungsi . '/' . $bulan . '/' . $tahun;

        // create product
        $surat = SuratTugas::create([
            'tujuan'         => $request->tujuan, //sama kyk perihal
            'bulan'    => $bulan,
            'tahun'      => $tahun,
            'tanggalsurat'   => Carbon::createFromFormat('m/d/Y', $tanggal)->format('Y-m-d'), // datepicker range start
            'tanggalmulai'   => Carbon::createFromFormat('m/d/Y', $request->tanggalmulai)->format('Y-m-d'), // datepicker range start
            'tanggalselesai'   => Carbon::createFromFormat('m/d/Y', $request->tanggalselesai)->format('Y-m-d'), // datepicker range start
            'nosurat' => $request->nosurat,
            'fungsi' => $request->fungsi,
            'nomorfull'   => $nomorfull,
        ]);
        $surat->user()->sync($request->user);
        return redirect()->route('hamuktisurattugas.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function suratTugasForm()
    {

        $users = User::all();
        // Membuat map dari kegiatan
        $users = $users->map(function ($user) {
            return [
                'name' => $user->name,
                'id' => $user->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array

        // Kirim data ke view
        return view('hamukti.hamuktisurattugasform', ['users' => $users]);
    }

    public function getSuratTugasById($id)
    {
        // Mencari presence berdasarkan ID
        $surattugas = SuratTugas::find($id);

        // Jika event tidak ditemukan
        if (!$surattugas) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $surattugasData = [
            'tujuan' => $surattugas->tujuan,
            'tanggal' => Carbon::parse($surattugas->tanggalsurat)->translatedFormat('d F Y'), // Menjadi format lokal "09 Oktober 2024 00:56:25"
            'nomorfull' => $surattugas->nomorfull,
            'id' => $surattugas->id,
        ];

        return response()->json($surattugasData);
    }
    public function suratTugasFormEdit($id)
    {

        $suratTugas = SuratTugas::find($id);

        // Jika event tidak ditemukan
        if (!$suratTugas) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $surat = [
            'tanggalsurat' => Carbon::parse($suratTugas->tanggalsurat)->format('m-d-Y'),
            'tanggalmulai' => Carbon::parse($suratTugas->tanggalmulai)->format('m-d-Y'),
            'tanggalselesai' => Carbon::parse($suratTugas->tanggalselesai)->format('m-d-Y'),
            'bulan' => $suratTugas->bulan,
            'tahun' => $suratTugas->tahun,
            'nosurat' => $suratTugas->nosurat,
            'fungsi' => $suratTugas->fungsi,
            'tujuan' => $suratTugas->tujuan,
            'id' => $suratTugas->id,

        ];
        $users = User::all();
        // Membuat map dari kegiatan
        $users = $users->map(function ($user) {
            return [
                'name' => $user->name,
                'id' => $user->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array


        // Kirim data ke view
        return view('hamukti.hamuktisurattugasformedit', ['surat' => $surat, 'users' => $users]);
    }
    public function update(Request $request, $id)
    {
        $surattugas = SuratTugas::findOrFail($id);


        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $kodeangka = $request->input('kodeangka');
        $nomorfull = $request->nosurat . '/' . $kodeangka . '/' . $request->fungsi . '/' . $bulan . '/' . $tahun;

        // dd($request);

        // Update data schedule
        $surat = $surattugas->update([
            'tujuan'      => $request->tujuan,
            'tanggalmulai'    => Carbon::createFromFormat('m/d/Y', $request->tanggalmulai)->format('Y-m-d'), // datepicker range start
            'tanggalselesai'    => Carbon::createFromFormat('m/d/Y', $request->tanggalselesai)->format('Y-m-d'), // datepicker range start
            'fungsi' => $request->fungsi,
            'nomorfull' => $nomorfull
        ]);


        $surattugas->user()->sync($request->user);

        return redirect()->route('hamuktisurattugas.index')->with(['success' => 'Data Berhasil Diupdate!'], 200);
    }
    public function delete($id)
    {
        $sk = SuratTugas::findOrFail($id);
        $sk->delete();

        // return redirect('agenkitaagenda.getEvents')->with('success', 'Presensi berhasil dihapus.');
        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
