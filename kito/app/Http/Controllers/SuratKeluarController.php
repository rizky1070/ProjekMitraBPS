<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Disposisi;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SuratKeluarController extends Controller
{
    //
    public function suratKeluarForm()
{
    // Dapatkan bulan dan tahun sekarang
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;

    // Set bulan dan tahun pada transaksi baru
    $bulan = $currentMonth;
    $tahun = $currentYear;

    // Cari nomor transaksi terakhir di bulan dan tahun yang sama
    $lastNomor = SuratKeluar::where('bulan', $currentMonth)
        ->where('tahun', $currentYear)
        ->orderBy('nomor', 'desc')
        ->first();

    // Set nomor transaksi: Jika tidak ada transaksi, mulai dari 1
    $newNomor = $lastNomor ? intval($lastNomor->nomor) + 1 : 1;

    // Format nomor transaksi menjadi 3 digit
    $nomor = str_pad($newNomor, 3, '0', STR_PAD_LEFT);

    // Buat array nomor surat
    $nomorSurat = [
        'bulan' => $bulan,
        'tahun' => $tahun,
        'nomor' => $nomor,
    ];

    // Ambil data instansi
    $instansis = Instansi::all();

    // Membuat map dari kegiatan
    $instansis = $instansis->map(function ($instansi) {
        return [
            'namasingkat' => $instansi->namasingkat,
        ];
    })->toArray(); // Mengubah hasil map menjadi array

    // Kirim data ke view
    return view('hamukti.hamuktisuratkeluarform', [
        'nomorSurat' => $nomorSurat,
        'instansis' => $instansis,
    ]);
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
        $query = SuratKeluar::query();

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
                $query->where('perihal', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('namainstansi', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }

        // Execute the query, paginate, and retrieve the results
        $suratkeluars = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Map the results to transform the 'absen' format
        $suratkeluars->getCollection()->transform(function ($suratkeluar) {
            return [ // yg ada di tampilan tabel
                'perihal' => $suratkeluar->perihal,
                'tanggal' => Carbon::parse($suratkeluar->created_at)->translatedFormat('d F Y H:i:s'),
                'namainstansi' => $suratkeluar->namainstansi,
                'nomorfull' => $suratkeluar->nomorfull,
                'id' => $suratkeluar->id,
            ];
        });

        // Return the view with paginated presences
        return view('hamukti.hamuktisuratkeluar', [
            'suratkeluars' => $suratkeluars,
            'pagination' => $suratkeluars // Pass the paginated data correctly to the view
        ]);
    }
    public function suratKeluarFormEdit($id)
    {

        $suratkeluar = SuratKeluar::find($id);

        // Jika event tidak ditemukan
        if (!$suratkeluar) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        $surat = [
            'tanggal' => Carbon::parse($suratkeluar->tanggal)->format('m-d-Y'),
            'nomorfull' => $suratkeluar->nomorfull,
            'perihal' => $suratkeluar->perihal,
            'id' => $suratkeluar->id,

        ];

        $instansis = Instansi::all();
        // Membuat map dari kegiatan
        $instansis = $instansis->map(function ($instansi) {
            return [
                'namasingkat' => $instansi->namasingkat,
                // 'id' => $instansi->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array

        // Kirim data ke view
        return view('hamukti.hamuktisuratkeluarformedit', ['surat' => $surat, 'instansis' => $instansis]);
    }
    public function getSuratKeluarById($id)
    {
        // Mencari presence berdasarkan ID
        $suratkeluar = SuratKeluar::find($id);

        // Jika event tidak ditemukan
        if (!$suratkeluar) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $suratkeluarData = [
            'perihal' => $suratkeluar->perihal,
            'tanggal' => Carbon::parse($suratkeluar->tanggal)->translatedFormat('d F Y'), // Menjadi format lokal "09 Oktober 2024 00:56:25"
            'file' => $suratkeluar->file,
            'namainstansi' => $suratkeluar->namainstansi,
            'nomorfull' => $suratkeluar->nomorfull,
            'id' => $suratkeluar->id,
        ];

        return response()->json($suratkeluarData);
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
        $tanggal = $request->input('suratkeluardate');
        $bulan = Carbon::createFromFormat('m/d/Y', $tanggal)->format('m'); // datepicker range start
        $tahun = Carbon::createFromFormat('m/d/Y', $tanggal)->format('Y'); // datepicker range start
        $kodekab = $request->input('kodekab');
        // Gabungkan variabel menjadi nomorfull
        $nomorfull = $request->jenis . $request->nomor . '/' . $kodekab . '/' . $request->kodeabjad . '.' . $request->kodeangka . '/' . $bulan . '/' . $tahun;

        $nama_instansi = $request->instansi;

        // create product
        SuratKeluar::create([
            'user_id'       => auth()->id(),
            'perihal'         => $request->perihal,
            'instansi_id'   => $request->instansi,
            'namainstansi' => $nama_instansi,
            'jenis' => $request->jenis,
            'bulan'    => $bulan,
            'tahun'      => $tahun,
            'tanggal'   => Carbon::createFromFormat('m/d/Y', $tanggal)->format('Y-m-d'), // datepicker range start
            'nomor' => $request->nomor,
            'kodeabjad' => $request->kodeabjad,
            'kodeangka'         => $request->kodeangka,
            'nomorfull'   => $nomorfull,
            'file'         => $file_name,
        ]);
        return redirect()->route('hamuktisuratkeluar.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function delete($id)
    {
        $suratkeluar = SuratKeluar::findOrFail($id);
        $suratkeluar->delete();

        return redirect('/hamuktisuratkeluar')->with('success', 'Presensi berhasil dihapus.');
        // return response()->route('hamuktisuratkeluar.index')->json(['message' => 'Event deleted successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $schedule = SuratKeluar::findOrFail($id);

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

        $nama_instansi = $request->instansi;

        // Update data schedule
        $schedule->update([
            'perihal'      => $request->perihal,
            'namainstansi' => $nama_instansi,
            'dokumen'       => $newFile, // Gunakan dokumen baru atau yang lama
        ]);

        return redirect()->route('hamuktisuratkeluar.index')->with(['success' => 'Data Berhasil Diupdate!'], 200);
    }
}
