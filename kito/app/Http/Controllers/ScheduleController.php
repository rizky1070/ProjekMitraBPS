<?php

namespace App\Http\Controllers;

//import return type redirectResponse
use App\Models\MeetingNote;
use App\Models\Presence;
use App\Models\Signature;
use Storage;
use View;
use Carbon\Carbon;
use App\Models\File;
use App\Models\Image;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;


class ScheduleController extends Controller
{
    //        // Direktori tempat gambar disimpan
    const UPLOAD_DIR = 'uploads/';
    // Ekstensi gambar yang diizinkan
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];
    // Ukuran maksimum file (dalam bytes)
    const MAX_FILE_SIZE = 5000000; // 5000 KB


    public function index()
    {
        $data = schedule::all();
        return view('agenkita.agenkitaagenda', compact('data'));
    }



    public function editFormAgenda($id)
    {


        // Mencari schedule berdasarkan ID
        $event = Schedule::find($id);

        // Jika event tidak ditemukan
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $eventData = [
            'title' => $event->kegiatan,
            'keterangan' => $event->keterangan,
            'start' => Carbon::parse($event->date_start)->format('m/d/Y'), // Mengubah format menjadi yyyy-mm-dd
            'end' => Carbon::parse($event->date_end)->format('m/d/Y'), // Mengubah format menjadi yyyy-mm-dd
            'time_start' => Carbon::parse($event->time_start)->format('H:i'), // Mengubah format waktu menjadi HH:MM
            'time_end' => Carbon::parse($event->time_end)->format('H:i'), // Mengubah format waktu menjadi HH:MM
            'id' => $event->id,
        ];

        // Kembali ke view dan mengirimkan data event
        return view('agenkita.agenkitaformeditagenda', ['event' => $eventData]);
    }


    public function create(): View
    {
        return view('agenkitaagenda');
    }

    public function store(Request $request): RedirectResponse{
        // Pastikan user sudah login
    if (!auth()->check()) {
        return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
    }
        if($request->hasFile('gambar')){
            $image = $request->file('gambar');
            $image->storeAs('public/uploads/images', $image->hashName());
            $image_name = $image->hashName();
        } else {
            $image_name = null;
        }

        // upload file
        if($request->hasFile('dokumen')){
            $file = $request->file('dokumen');
            $file->storeAs('public/uploads/docs', $file->hashName());
            $file_name = $file->hashName();
        } else {
            $file_name = null;
        }

        // create product
        Schedule::create([
            'user_id'       => auth()->id(),
            'gambar'         => $image_name,
            'date_start'    => Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d'), // datepicker range start
            'date_end'      => Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d'), // datepicker range end
            'time_start' => $request->timestart, //  starttime
            'time_end' => $request->timeend, //  endtime
            'kegiatan'         => $request->kegiatan,
            'keterangan'   => $request->keterangan,
            'dokumen'         => $file_name,
            // 'stock'         => $request->stock
        ]);
        return redirect()->route('agenkitaagenda.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function getEvents()
    {
        $schedules = schedule::all();
        $events = $schedules->map(function($event){
            return [
                'title' => $event->kegiatan,
                'start' => Carbon::parse($event->date_start . ' ' . $event->time_start)->toIso8601String(), // Menggabungkan dengan Carbon
                'end' => Carbon::parse($event->date_end . ' ' . $event->time_end)->toIso8601String(), // Menggabungkan dengan Carbon
                'id' => $event->id,
            ];
        });
        return response()->json($events);
    }

    public function getEventById($id)
    {
        // Mencari schedule berdasarkan ID
        $event = Schedule::find($id);

        // Jika event tidak ditemukan
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $eventData = [
            'title' => $event->kegiatan,
            'start' => Carbon::parse($event->date_start)->format('Y-m-d'), // Mengubah format menjadi yyyy-mm-dd
            'end' => Carbon::parse($event->date_end)->format('Y-m-d'), // Mengubah format menjadi yyyy-mm-dd
            'time_start' => Carbon::parse($event->time_start)->format('H:i'), // Mengubah format waktu menjadi HH:MM
            'time_end' => Carbon::parse($event->time_end)->format('H:i'), // Mengubah format waktu menjadi HH:MM
            'gambar' => $event->gambar,
            'dokumen' => $event->dokumen,
            'id' => $event->id,
        ];

        return response()->json($eventData);
    }

    public function deleteEvent($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        // return redirect('agenkitaagenda.getEvents')->with('success', 'Presensi berhasil dihapus.');
        return response()->json(['message' => 'Event deleted successfully'], 200);
    }


    public function update(Request $request, $id)
{
    $schedule = Schedule::findOrFail($id);

    // Proses gambar
    if ($request->hasFile('gambar')) {
        if ($schedule->gambar && Storage::exists('public/uploads/docs/' . $schedule->gambar)) {
            Storage::delete('public/uploads/images/' . $schedule->gambar);
        }
        $image = $request->file('gambar');
        // Upload dan simpan gambar baru
        $image->storeAs('public/uploads/images', $image->hashName());

        // Update kolom gambar dengan gambar baru
        $newImage = $image->hashName();
    } else {
        // Jika tidak ada gambar baru, gunakan gambar lama
        $newImage = $schedule->gambar;
    }

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
        'gambar'        => $newImage, // Gunakan gambar baru atau yang lama
        'date_end'      => Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d'),
        'time_end'      => $request->timeend,
        'kegiatan'      => $request->kegiatan,
        'keterangan'    => $request->keterangan,
        'dokumen'       => $newFile, // Gunakan dokumen baru atau yang lama
    ]);

    // update data pada presence
    Presence::where('schedule_id', $schedule->id)->update([
        'kegiatan' => $schedule->kegiatan,
    ]);
    MeetingNote::where('schedule_id', $schedule->id)->update([
        'kegiatan' => $schedule->kegiatan,
    ]);

    return redirect()->route('agenkitaagenda.index')->with(['success' => 'Data Berhasil Diupdate!'], 200);
}

}
