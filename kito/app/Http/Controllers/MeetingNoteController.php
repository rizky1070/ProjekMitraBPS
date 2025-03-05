<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\File;
use App\Models\User;
use App\Models\Schedule;
use App\Models\MeetingNote;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class MeetingNoteController extends Controller
{

    public function index(Request $request)
    {
        $perPage = 10; // Change this number as needed

        // Get the search input from the request
        $search = $request->input('search');

        // Query the MeetingNote model, filtering by kegiatan and related user's name if a search term is provided
        $query = MeetingNote::query();

        if ($search) {
            $query->where('kegiatan', 'like', '%' . $search . '%');
        }

        // Execute the query, paginate, and retrieve the results
        $notes = $query->paginate($perPage);
        // $data = $query->get(); // Fetch the filtered or complete data

       $notes->getCollection()->transform(function ($note) {
            // Find the related user via the relationship
            $user = User::find($note->user_id);

            return [
                'kegiatan' => $note->kegiatan,
                'notulen' => Carbon::parse($note->notulen)->translatedFormat('d F Y H:i'),
                'name' => $user ? $user->name : 'Tidak diketahui', // Ensure user is found
                'file_path' => '/storage/uploads/docs/' . $note->filekelengkapan,
                'id' => $note->id,
            ];
        });

        return view('agenkita.agenkitanotulen', ['notes' => $notes, 'pagination' => $notes]);
    }

    public function getEventsNotulen()
    {
        // Mengambil tanggal saat ini
        $today = Carbon::today();
        // dd($today);

        // Mendapatkan tanggal seminggu yang lalu
        $oneWeekAgo = Carbon::today()->subWeek(); // Mengurangi satu minggu dari tanggal hari ini

        // Mengambil kegiatan yang dimulai dari seminggu yang lalu hingga hari ini
        $schedules = Schedule::where('date_start', '>=', $oneWeekAgo)
            ->where('date_start', '<=', $today)
            ->get();

        // Membuat map dari kegiatan
        $events = $schedules->map(function ($event) {
            return [
                'title' => $event->kegiatan,
                'id' => $event->id,
            ];
        })->toArray(); // Mengubah hasil map menjadi array

        // Kirim data ke view
        return view('agenkita.agenkitaformnotulen', ['events' => $events]);
    }

    public function getNotulenById($id)
    {
        // Mencari schedule berdasarkan ID
        $event = MeetingNote::find($id);

        // Jika event tidak ditemukan
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        // Mengembalikan data event dalam format JSON
        $user = User::find($event->id);
        $eventData = [
            'kegiatan' => $event->kegiatan,
            'notulen' => Carbon::parse($event->notulen)->translatedFormat('d F Y H:i'), // Format lokal "09 Oktober 2024 00:56"
            'user_id' => $user->name,
            'id' => $event->id,
        ];

        return response()->json($eventData);
    }

    public function getMeetingNotes()
    {
        $notulens = MeetingNote::all();
        $events = $notulens->map(function($event){
            return [
                'kegiatan' => $event->kegiatan,
                'name' => $event->name,
                'catatan' => $event->catatan,
                'id' => $event->id,
            ];
        });
        return response()->json($events);
    }

    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        $file = $request->file('dokumen');
        $file->storeAs('public/uploads/docs', $file->hashName());

        $idd = $request->kegiatan; // ini harusnya diubah sesuai value inputnya 
        $id_kegiatan = Schedule::find($idd);
        $nama_kegiatan = $id_kegiatan->kegiatan;

        // create product
        MeetingNote::create([
            'user_id'       => auth()->id(),
            'catatan'       => $request->catatan,
            'schedule_id'   => $request->kegiatan,
            'notulen' => Carbon::parse($request->notulensidate . ' ' . $request->notulensitime), //  starttime
            'kegiatan'         => $nama_kegiatan,
            'filekelengkapan'         => $file->hashName(),
        ]);
        return redirect()->route('agenkitanotulen.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }


    public function update(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        // Temukan MeetingNote berdasarkan ID (misalnya $id di URL atau request)
        $meetingNote = MeetingNote::findOrFail($request->id); // Pastikan kamu mendapatkan ID yang benar

        // Cek apakah ada file yang di-upload
        if ($request->hasFile('dokumen')) {
            if ($meetingNote->filekelengkapan && Storage::exists('public/uploads/docs/' . $meetingNote->filekelengkapan)) {
                Storage::delete('public/uploads/docs/' . $meetingNote->filekelengkapan);
            }
            // Proses file baru
            $file = $request->file('dokumen');
            $file->storeAs('public/uploads/docs', $file->hashName());

            // Menggunakan file path baru jika ada upload
            $newFilePath = $file->hashName();
            $meetingNote->update([
                'user_id'       => auth()->id(),
                'catatan'       => $request->catatan,
                'filekelengkapan'       => $newFilePath, // Gunakan file baru atau lama
            ]);
        }
        else {
            $meetingNote->update([
                'user_id'       => auth()->id(),
                'catatan'       => $request->catatan,
            ]);
        }

        return redirect()->route('agenkitanotulen.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

public function editFormNotulen($id)
    {
        // Mencari schedule berdasarkan ID
        $event = MeetingNote::find($id);

        // Jika event tidak ditemukan
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $eventData = [
            'kegiatan' => $event->kegiatan,
            // Pisahkan tanggal dan waktu dari 'notulen'
            'notulen_date' => Carbon::parse($event->notulen)->format('d/m/Y'), // Format: "10/08/2024"
            'notulen_time' => Carbon::parse($event->notulen)->format('H:i'),   // Format: "16:33"
            'schedule_id' => $event->schedule_id,
            'catatan' => $event->catatan,
            'id' => $event->id,
        ];


        // Kembali ke view dan mengirimkan data event
        return view('agenkita.agenkitaformeditnotulen', ['event' => $eventData]);
    }

public function delete($id)
{
    $notulen = MeetingNote::findOrFail($id);
    $notulen->delete();

    return redirect()->back()->with('success', 'Notulen berhasil dihapus.');
    // return response()->json(['message' => 'Notulen deleted successfully'], 200);
}
}
