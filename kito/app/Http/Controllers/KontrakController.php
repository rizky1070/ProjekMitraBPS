<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kontrak;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class KontrakController extends Controller
{
    //
    public function KontrakForm()
    {
        // Dapatkan bulan dan tahun sekarang
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;



        // Set bulan dan tahun pada transaksi baru
        $bulan = $currentMonth;
        $tahun = $currentYear;

        // Cari nomor transaksi terakhir di bulan dan tahun yang sama
        $lastNomor = Kontrak::where('bulan', $currentMonth)
            ->where('tahun', $currentYear)
            ->orderBy('nosurat', 'desc')
            ->first();

        // Set nomor transaksi: Jika tidak ada transaksi, mulai dari 1
        $nomor = $lastNomor ? $lastNomor->nosurat + 1 : 1;

        $nomorSurat = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nomor' => $nomor,
        ];
        return view('hamukti.hamuktikontrakform', ['nomorSurat' => $nomorSurat]);
    }

    public function index(Request $request)
    {
        // Set the number of items per page
        $perPage = 10; // Change this number as needed

        // Retrieve the start and end dates from the request
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $searchKeyword = $request->input('search'); // Ambil kata kunci pencarian

        // Initialize the query
        $query = Kontrak::query();

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('tanggal', '>=', $startDate)
                ->where('tanggal', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('uraian', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('suratfull', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }

        // Execute the query, paginate, and retrieve the results
        $kontraks = $query->orderBy('tanggal', 'desc')->paginate($perPage);

        // Map the results to transform the 'absen' format
        $kontraks->getCollection()->transform(function ($kontrak) {
            return [ // yg ada di tampilan tabel
                'uraian' => $kontrak->uraian,
                'tanggal' => Carbon::parse($kontrak->tanggal)->translatedFormat('d F Y H:i:s'),
                'suratfull' => $kontrak->suratfull,
                'fungsi' => $kontrak->fungsi,
                'id' => $kontrak->id,
            ];
        });

        // Return the view with paginated presences
        return view('hamukti.hamuktikontrak', [
            'kontraks' => $kontraks,
            'pagination' => $kontraks // Pass the paginated data correctly to the view
        ]);
    }

    public function kontrakFormEdit($id)
    {

        $kontrak = Kontrak::find($id);

        // Jika event tidak ditemukan
        if (!$kontrak) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        $surat = [
            'tanggal' => Carbon::parse($kontrak->tanggal)->format('m-d-Y'),
            'nosurat' => $kontrak->nosurat,
            'fungsi' => $kontrak->fungsi,
            'kodesurat' => $kontrak->kodesurat,
            'suratfull' => $kontrak->suratfull,
            'uraian' => $kontrak->uraian,
            'id' => $kontrak->id,

        ];

        // Kirim data ke view
        return view('hamukti.hamuktikontrakformedit', ['surat' => $surat]);
    }

    public function getKontrakById($id)
    {
        // Mencari presence berdasarkan ID
        $kontrak = Kontrak::find($id);

        // Jika event tidak ditemukan
        if (!$kontrak) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $kontrakData = [
            'uraian' => $kontrak->uraian,
            'tanggal' => Carbon::parse($kontrak->tanggal)->translatedFormat('d F Y'), // Menjadi format lokal "09 Oktober 2024 00:56:25"
            'file' => $kontrak->file,
            'suratfull' => $kontrak->suratfull,
            'id' => $kontrak->id,
        ];

        return response()->json($kontrakData);
    }

    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        // upload file
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $file->storeAs('public/uploads/docs', $file->hashName());
            $file_name = $file->hashName();
        } else {
            $file_name = null;
        }

        //ambil bulan dan tahun
        $tanggal = $request->input('tanggal');
        $bulan = Carbon::now()->month; // datepicker range start
        $tahun = Carbon::now()->year; // datepicker range start
        // Gabungkan variabel menjadi nomorfull
        $nomorfull = $request->nosurat . '/' . $request->kodesurat;

        // dd($bulan);
        // create product
        Kontrak::create([
            'uraian'         => $request->uraian,
            'fungsi' => $request->fungsi,
            'bulan'    => $bulan,
            'tahun'      => $tahun,
            'tanggal'   => Carbon::createFromFormat('m/d/Y', $tanggal)->format('Y-m-d'), // datepicker range start
            'kodesurat' => $request->kodesurat,
            'nosurat' => $request->nosurat,
            'suratfull'   => $nomorfull,
            'file'         => $file_name,
        ]);
        return redirect()->route('hamuktikontrak.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function delete($id)
    {
        $suratkeluar = Kontrak::findOrFail($id);
        $suratkeluar->delete();

        return redirect('/hamuktikontrak')->with('success', 'Kontrak berhasil dihapus.');
        // return response()->route('hamuktisuratkeluar.index')->json(['message' => 'Event deleted successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $schedule = Kontrak::findOrFail($id);

        // Proses dokumen
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            // Upload dan simpan dokumen baru
            $file->storeAs('public/uploads/docs', $file->hashName());

            // Update kolom dokumen dengan dokumen baru
            $newFile = $file->hashName();
        } else {
            // Jika tidak ada dokumen baru, gunakan dokumen lama
            $newFile = $schedule->dokumen;
        }

        // Update data schedule
        $schedule->update([
            'uraian'      => $request->uraian,
            'fungsi'       => $request->fungsi,
            'dokumen'       => $newFile, // Gunakan dokumen baru atau yang lama
        ]);

        return redirect()->route('hamuktikontrak.index')->with(['success' => 'Data Berhasil Diupdate!'], 200);
    }
}
