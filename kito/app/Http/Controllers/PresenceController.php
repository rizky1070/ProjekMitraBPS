<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\PDF;
use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Presence;
use App\Models\Schedule;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        // Set the number of items per page
        $perPage = 10; // Change this number as needed

        // Retrieve the start and end dates from the request
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $searchKeyword = $request->input('search'); // Ambil kata kunci pencarian

        // Initialize the query
        $query = Presence::query();

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('absen', '>=', $startDate)
                ->where('absen', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('kegiatan', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('name', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }

        // Execute the query, paginate, and retrieve the results
        $presences = $query->orderBy('absen', 'desc')->paginate($perPage);
        
          // Maintain query string for filters during pagination
    $presences->appends($request->all());

        // Map the results to transform the 'absen' format
        $presences->getCollection()->transform(function ($presence) {
            return [ // yg ada di tampilan tabel
                'kegiatan' => $presence->kegiatan,
                'absen' => Carbon::parse($presence->absen)->translatedFormat('d F Y H:i:s'),
                'signature' => $presence->signature,
                'lokasi' => $presence->lokasi,
                'jabatan' => $presence->jabatan,
                'name' => $presence->name,
                'id' => $presence->id,
            ];
        });

        // Return the view with paginated presences
        return view('agenkita.agenkitapresensi', [
            'presences' => $presences,
            'pagination' => $presences // Pass the paginated data correctly to the view
        ]);
    }
    public function getEventsPresensi()
    {
        // Mengambil tanggal saat ini
        $today = Carbon::today();
        // dd($today);

        // Mengambil kegiatan yang dimulai dari hari ini atau setelahnya
        $schedules = schedule::where('date_end', '>=', $today)->get();

        // Membuat map dari kegiatan
        $presences = $schedules->map(function ($presence) {
            return [
                'title' => $presence->kegiatan,
                'id' => $presence->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array

        // Kirim data ke view
        return view('agenkita.agenkitaformpresensi', ['presences' => $presences]);
    }

    public function getEventsPresensiAdmin()
    {


        // Mengambil kegiatan yang dimulai dari hari ini atau setelahnya
        $schedules = schedule::all();
        $users = User::all();
        // Membuat map dari kegiatan
        $presences = $schedules->map(function ($presence) {
            return [
                'title' => $presence->kegiatan,
                'id' => $presence->id,

            ];
        })->toArray(); // Mengubah hasil map menjadi array
        $users = $users->map(function ($user) {
            return [
                'nama' => $user->name,
                'jabatan' => $user->jabatan,
                'id' => $user->id,

            ];
        })->toArray(); // Mengubah hasil map menjadi array

        // Kirim data ke view
        return view('agenkita.agenkitaformpresensiadmin', ['presences' => $presences, 'users' => $users]);
    }
    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']); // diganti path absen
        }

        // Mengonversi data URL tanda tangan ke dalam format gambar
        $signatureData = $request->input('signature');
        $image = str_replace('data:image/png;base64,', '', $signatureData);
        $image = str_replace(' ', '+', $image); // Mengganti spasi dengan plus
        $imageName = time() . '.png'; // Nama file untuk tanda tangan dengan format waktu

        // Simpan gambar tanda tangan ke dalam folder storage/public/signatures
        Storage::disk('public')->put('uploads/signatures/' . $imageName, base64_decode($image));

        // $signature_path = 'uploads/signatures/' . $imageName; // Simpan path gambar tanda tangan
        $signature_path = $imageName; // Simpan path gambar tanda tangan

        // $signature = $request->file('ttd'); //ttd diubah nama ttd UI
        // $signature->storeAs('public/uploads/signatures', $signature->hashName());

        $idd = $request->kegiatan;
        $id_kegiatan = Schedule::find($idd);
        $nama_kegiatan = $id_kegiatan->kegiatan;

        Presence::create([
            'user_id'       => auth()->id(),
            'schedule_id'      => $request->kegiatan,
            'signature'         => $signature_path,
            'absen'             => Carbon::parse($request->presensidate . ' ' . $request->presensitime),
            'lokasi' => $request->lokasi, //  starttime
            'name' => $request->nama, //  endtime
            'kegiatan'         => $nama_kegiatan,
            'jabatan'         => $request->jabatan,
        ]);
        return redirect()->route('agenkitapresensi.index')->with(['success' => 'Data Berhasil Disimpan!']); //diganti route presence
    }



    public function deletePresence($id)
    {
        $presence = Presence::findOrFail($id);
        $presence->delete();

        return redirect('agenkitapresensi')->with('success', 'Presensi berhasil dihapus.');
    }

    public function getPresenceById($id)
    {
        // Mencari presence berdasarkan ID
        $presence = Presence::find($id);

        // Jika event tidak ditemukan
        if (!$presence) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $presenceData = [
            'signature' => $presence->signature,
            'absen' => Carbon::parse($presence->absen)->translatedFormat('d F Y H:i:s'), // Menjadi format lokal "09 Oktober 2024 00:56:25"
            'lokasi' => $presence->lokasi,
            'nama' => $presence->name,
            'kegiatan' => $presence->kegiatan,
            'jabatan' => $presence->jabatan,
            'id' => $presence->id,
        ];

        return response()->json($presenceData);
    }

    public function pdf_export_get(Request $request)
    {
        // dd($request);
        // Mengambil nilai dari query string
        $searchKeyword = $request->query('search'); // atau $request->input('search');
        $startDateInput = $request->query('start_date'); // atau $request->input('start');
        $endDateInput = $request->query('end_date'); // atau $request->input('end');

        // Initialize the query
        $query = Presence::query();

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('absen', '>=', $startDate)
                ->where('absen', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('kegiatan', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('name', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }

        // Execute the query, paginate, and retrieve the results
        $presences = $query->orderBy('absen', 'desc')->get();
        // dd($presences);
        // $presences = Presence::get();
        // $presence = $presences->attributes;
        // dd($presences);
        // Jika tidak ada data yang ditemukan, berikan nilai default untuk nama kegiatan
        if ($presences->isEmpty()) {
            $actt = 'laporan';
        } else {
            $act = $presences->first();
            $actt = $act->kegiatan; // Mengambil kegiatan pertama
        }
        // dd($actt);
        $datas = [];
        foreach ($presences as $presence) {
            $datas[] = [
                'signature' => $presence->signature,
                'absen' => Carbon::parse($presence->absen)->translatedFormat('d F Y H:i:s'), // Menjadi format lokal "09 Oktober 2024 00:56:25"
                'nama' => $presence->name,
                'kegiatan' => $presence->kegiatan,
                'jabatan' => $presence->jabatan,
            ];
        }
        // dd($datas);
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('agenkita.absenrapat', ['datas' => $datas, 'actt' => $actt])->setPaper('a4', 'landscape');
        $filename = str_replace(' ', '_', strtolower($actt)) . '_Laporan Absensi.pdf';
        return $pdf->download($filename);
    }
}
