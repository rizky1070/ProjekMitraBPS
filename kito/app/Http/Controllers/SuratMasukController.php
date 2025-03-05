<?php

namespace App\Http\Controllers;

use App\Models\SuratMasukDisposisi;
use Carbon\Carbon;
use App\Models\Instansi;
use App\Models\Disposisi;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SuratMasukController extends Controller
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

        // Initialize the query
        $query = SuratMasuk::query();

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('tglterima', '>=', $startDate)
                ->where('tglterima', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('perihal', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('namainstansi', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }

        // Execute the query, paginate, and retrieve the results
        $suratmasuks = $query->orderBy('tglterima', 'desc')->paginate($perPage);


        // Map the results to transform the 'absen' format
        $suratmasuks->getCollection()->transform(function ($suratmasuk) {
            $disposisiIds = SuratMasukDisposisi::where('suratmasuk_id', $suratmasuk->id)->pluck('disposisi_id');
            $nama_disposisi = Disposisi::whereIn('id', $disposisiIds)->pluck('namadisposisi')->toArray();
            // Gabungkan semua nama disposisi menjadi string
            $nama_disposisi_string = implode(', ', $nama_disposisi);
            return [ // yg ada di tampilan tabel
                'perihal' => $suratmasuk->perihal,
                'tglterima' => Carbon::parse($suratmasuk->tglterima)->translatedFormat('d F Y '),
                'namadisposisi' => $nama_disposisi_string,
                'namainstansi' => $suratmasuk->namainstansi,
                'nosurat' => $suratmasuk->nosurat,
                'id' => $suratmasuk->id,
            ];
        });

        // Return the view with paginated presences
        return view('hamukti.hamuktisuratmasuk', [
            'suratmasuks' => $suratmasuks,
            'pagination' => $suratmasuks // Pass the paginated data correctly to the view
        ]);
    }
    public function suratMasukFormEdit($id)
    {

        $suratmasuk = SuratMasuk::find($id);

        // Jika event tidak ditemukan
        if (!$suratmasuk) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $surat = [
            'tglterima' => Carbon::parse($suratmasuk->tglterima)->format('m-d-Y'),
            'tglsurat' => Carbon::parse($suratmasuk->tglsurat)->format('m-d-Y'),
            'nosurat' => $suratmasuk->nosurat,
            'namainstansi' => $suratmasuk->namainstansi,
            'perihal' => $suratmasuk->perihal,
            'id' => $suratmasuk->id,

        ];

        $instansis = Instansi::all();
        // Membuat map dari kegiatan
        $instansis = $instansis->map(function ($instansi) {
            return [
                'namasingkat' => $instansi->namasingkat,

            ];
        })->toArray(); // Mengubah hasil map menjadi array

        $disposisis = Disposisi::all();
        // Membuat map dari kegiatan
        $disposisis = $disposisis->map(function ($disposisi) {
            return [
                'namadisposisi' => $disposisi->namadisposisi,
                'id' => $disposisi->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array

        // Kirim data ke view
        return view('hamukti.hamuktisuratmasukformedit', ['disposisis' => $disposisis, 'surat' => $surat, 'instansis' => $instansis]);
    }
    public function suratMasukForm()
    {

        $instansis = Instansi::all();
        // Membuat map dari kegiatan
        $instansis = $instansis->map(function ($instansi) {
            return [
                'namasingkat' => $instansi->namasingkat,
                'id' => $instansi->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array

        $disposisis = Disposisi::all();
        // Membuat map dari kegiatan
        $disposisis = $disposisis->map(function ($disposisi) {
            return [
                'namadisposisi' => $disposisi->namadisposisi,
                'id' => $disposisi->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array

        // Kirim data ke view
        return view('hamukti.hamuktisuratmasukform', ['disposisis' => $disposisis, 'instansis' => $instansis]);
    }
    public function getSuratMasukById($id)
    {
        // Mencari presence berdasarkan ID
        $suratmasuk = SuratMasuk::find($id);

        // Jika event tidak ditemukan
        if (!$suratmasuk) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $suratmasukData = [
            'perihal' => $suratmasuk->perihal,
            'tglterima' => Carbon::parse($suratmasuk->tglterima)->translatedFormat('d F Y'), // Menjadi format lokal "09 Oktober 2024 00:56:25"
            'file' => $suratmasuk->file,
            'namainstansi' => $suratmasuk->namainstansi,
            'nosurat' => $suratmasuk->nosurat,
            'id' => $suratmasuk->id,
        ];
        // dd($suratmasukData->tglterima);
        return response()->json($suratmasukData);
    }
    public function store(Request $request): RedirectResponse{
        // Pastikan user sudah login
    if (!auth()->check()) {
        return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
    }

        // upload file
        if($request->hasFile('dokumen')){
            $file = $request->file('dokumen');
            $file->storeAs('public/uploads/docs', $file->hashName());
            $file_name = $file->hashName();
        } else {
            $file_name = null;
        }

        $nama_instansi = $request->instansi;

        // create product
        $surat = SuratMasuk::create([
            'perihal'         => $request->perihal,
            'namainstansi' => $nama_instansi,
            'tglterima'    => Carbon::createFromFormat('m/d/Y', $request->tglterima)->format('Y-m-d'), // datepicker range start
            'tglsurat'      => Carbon::createFromFormat('m/d/Y', $request->tglsurat)->format('Y-m-d'), // datepicker range end
            'nosurat' => $request->nosurat,
            'disposisi_id' => $request->disposisi,
            'uraiandisposisi'  => $request->uraiandisposisi,
            'file'         => $file_name,
        ]);

        $surat->disposisi()->sync($request->disposisi);

        return redirect()->route('hamuktisuratmasuk.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
    
    public function delete($id)
    {
        $suratmasuk = SuratMasuk::findOrFail($id);
        $suratmasuk->delete();

        // return redirect('agenkitaagenda.getEvents')->with('success', 'Presensi berhasil dihapus.');
        return redirect('/hamuktisuratmasuk')->with('success', 'Presensi berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $suratmasuk = SuratMasuk::findOrFail($id);

        // Proses dokumen
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            // Upload dan simpan dokumen baru
            $file->storeAs('public/uploads/docs', $file->hashName());

            // Update kolom dokumen dengan dokumen baru
            $newFile = $file->hashName();
        } else {
            // Jika tidak ada dokumen baru, gunakan dokumen lama
            $newFile = $suratmasuk->dokumen;
        }

        // dd($request);

        // Update data schedule
        $surat = $suratmasuk->update([
            'perihal'      => $request->perihal,
            'tglterima'    => Carbon::createFromFormat('m/d/Y', $request->tglterima)->format('Y-m-d'), // datepicker range start
            'uraiandisposisi' => $request->uraiandisposisi,
            'dokumen'       => $newFile, // Gunakan dokumen baru atau yang lama
        ]);


        $suratmasuk->disposisi()->sync($request->disposisi);

        return redirect()->route('hamuktisuratmasuk.index')->with(['success' => 'Data Berhasil Diupdate!'], 200);
    }
}
