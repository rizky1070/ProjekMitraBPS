<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Izinkeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        // 1. Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }

        // 2. Validasi request (opsional tapi sangat direkomendasikan)
        $request->validate([
            'tanggalizin' => 'required|date',
            'jamizin' => 'required',
            'keperluan' => 'required|string|max:255',
        ]);

        // 3. Buat dan simpan data izin keluar
        $izin = Izinkeluar::create([
            'user_id'     => Auth()->user()->id,
            'tanggalizin' => $request->tanggalizin,
            'jamizin'     => $request->jamizin,
            'keperluan'   => $request->keperluan,
            'status'      => 1, // Status awal, misal: 1 = Diajukan
        ]);

        // --- AWAL BAGIAN NOTIFIKASI WHATSAPP ---

        // 4. Mengambil data untuk notifikasi
        $pemohon = Auth::user();
        
        // Ganti dengan email admin yang akan menerima notifikasi
         $adminEmails = ['siti@example.com', 'budi@example.com']; // Tambahkan email admin kedua di sini
        $admins = User::whereIn('email', $adminEmails)->get();

        // 5. Kirim notifikasi WhatsApp jika admin ditemukan
        if ($admins->isNotEmpty()) {
        foreach ($admins as $admin) {
            $this->sendWhatsAppIzinKeluar(
                $admin,
                $pemohon->name,
                $izin->tanggalizin,
                $izin->jamizin,
                $izin->keperluan
            );
        }
        } else {
            // Opsional: catat ke log jika admin tidak ditemukan
            Log::warning('Admin dengan email target tidak ditemukan untuk notifikasi izin keluar.');
        }

        // --- AKHIR BAGIAN NOTIFIKASI WHATSAPP ---

        // 6. Redirect kembali dengan pesan sukses
        return redirect()->back()->with(['success' => 'Data Izin Berhasil Diajukan!']);
    }

    private function sendWhatsAppIzinKeluar($admin, $namaPemohon, $tanggalIzin, $jamIzin, $keperluan)
    {
        // Ganti dengan token Fonnte Anda dari file .env
        $token = env('FONNTE_TOKEN');

        // Lakukan pembersihan nomor telepon untuk memastikan formatnya benar
        $targetPhoneNumber = $this->sanitizePhoneNumber($admin->nomer_telepon);

        if (!$targetPhoneNumber) {
            Log::warning('Nomor HP admin tidak valid atau tidak ditemukan.', ['admin_id' => $admin->id]);
            return;
        }

        // Jeda pengiriman acak untuk meniru perilaku manusia
        sleep(rand(1, 3));

        // Atur bahasa Carbon ke Indonesia untuk format tanggal
        Carbon::setLocale('id');
        $tanggalFormatted = Carbon::parse($tanggalIzin)->translatedFormat('l, d F Y');

        // Variasi Pesan (Spintax) untuk menghindari pemblokiran
        $header = [
            "🔔 *Notifikasi Izin Keluar Baru* 🔔",
            "🚶‍♂️ *Ada Pengajuan Izin Keluar* 🚶‍♀️",
            "📝 *Pemberitahuan Izin Keluar* 📝"
        ];
        
        $sapaan = ["Halo *{$admin->name}*,", "Hi *{$admin->name}*,", "Yth. *{$admin->name}*,"];
        $penutup = ["Mohon untuk segera ditindaklanjuti.", "Harap segera diproses.", "Terima kasih atas perhatiannya."];

        $randomHeader = $header[array_rand($header)];
        $randomSapaan = $sapaan[array_rand($sapaan)];
        $randomPenutup = $penutup[array_rand($penutup)];

        // Susun pesan notifikasi
        $message  = "{$randomHeader}\n\n";
        $message .= "{$randomSapaan}\n";
        $message .= "Ada pengajuan izin keluar baru yang perlu diperiksa:\n\n";
        $message .= "👤 *Nama Karyawan:* {$namaPemohon}\n";
        $message .= "📅 *Tanggal Izin:* {$tanggalFormatted}\n";
        $message .= "⏰ *Jam Kembali:* {$jamIzin}\n";
        $message .= "📄 *Keperluan:* {$keperluan}\n\n";
        $message .= $randomPenutup;

        // Persiapkan payload untuk dikirim ke Fonnte
        $payload = [
            'target'      => $targetPhoneNumber,
            'message'     => $message,
            'countryCode' => '62',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_HTTPHEADER     => ['Authorization: ' . $token],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // Catat response atau error untuk debugging
        if ($err) {
            Log::error('cURL Error saat kirim WA (Fonnte) untuk Izin Keluar: ' . $err, ['payload' => $payload]);
        } else {
            Log::info('Fonnte API Response (Izin Keluar): ' . $response, ['payload' => $payload]);
        }
    }


    private function sanitizePhoneNumber($number)
    {
        if (empty($number)) {
            return null;
        }
        // Hapus karakter selain angka
        $number = preg_replace('/[^0-9]/', '', $number);
        // Jika diawali 0, ganti dengan 62
        if (substr($number, 0, 1) == '0') {
            return '62' . substr($number, 1);
        }
        // Jika tidak diawali 62, tambahkan 62 di depan (asumsi nomor lokal)
        if (substr($number, 0, 2) != '62') {
             return '62' . $number;
        }
        
        // Anggap nomor valid jika panjangnya antara 10-15 digit setelah diformat
        if (strlen($number) < 10 || strlen($number) > 15) {
            return null;
        }

        return $number;
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
        $user_id = auth()->user()->id; // Menggunakan helper auth() lebih singkat

        // Mengambil data terakhir dari Izinkeluar berdasarkan user_id dan status 1 (izin)
        $izinTerakhir = Izinkeluar::where('user_id', $user_id)
            ->where('status', 1) // status 'izin' dengan kode 1
            ->orderBy('created_at', 'desc')
            ->first(); // Ambil yang pertama (terbaru)

        if ($izinTerakhir) {
            // [MODIFIKASI] Kirim seluruh object $izinTerakhir ke view
            // Ini akan membawa semua data termasuk id, jamizin, dll.
            return view('konfirmasiizin', ['izin' => $izinTerakhir]);
        } else {
            // Jika tidak ada data izin aktif, arahkan ke form pengajuan izin
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