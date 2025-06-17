<?php

namespace App\Http\Controllers;

use App\Notifications\NotifikasiPermintaanBarang;
use PDF;
use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\PermintaanBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Storage;

class PermintaanBarangController extends Controller
{
    //
    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }
        // Mengonversi data URL tanda tangan ke dalam format gambar
        $signatureData = $request->input('ttduser');
        $image = str_replace('data:image/png;base64,', '', $signatureData);
        $image = str_replace(' ', '+', $image); // Mengganti spasi dengan plus
        $imageName = time() . '.png'; // Nama file untuk tanda tangan dengan format waktu

        // Simpan gambar tanda tangan ke dalam folder storage/public/signatures
        Storage::disk('public')->put('uploads/signatures/' . $imageName, base64_decode($image));

        // $signature_path = 'uploads/signatures/' . $imageName; // Simpan path gambar tanda tangan
        $signature_path = $imageName;
        // dd($request);
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambar->storeAs('public/uploads/images', $gambar->hashName());
            $image_name = $gambar->hashName();
        } else {
            $image_name = null;
        }

        // dd($request);
        $status = 'disapproved';

        // create product
        PermintaanBarang::create([
            'ttduser'         => $signature_path,
            'stokpermintaan' => $request->stokpermintaan,
            'barang_id' => $request->barang_id,
            'user_id' => Auth()->user()->id,
            'status' => $status,
            'orderdate' => $request->orderdate,
            'buktifoto' => $image_name,
            'catatan' => $request->catatan,
        ]);

        // Mengambil data untuk notifikasi
        $peminta = Auth::user();
        $admin = User::where('email', 'siti@example.com')->first(); // Gunakan first() agar tidak error jika tidak ada
        $barang = Barang::findOrFail($request->barang_id);

        // Kirim notifikasi WhatsApp jika admin ditemukan
        if ($admin) {
            $this->sendWhatsAppPermintaanBarang(
                $admin,
                $peminta->name,
                $barang->namabarang,
                $request->stokpermintaan,
                $request->catatan, // Tambahkan parameter ini
                $request->orderdate
            );
        } else {
            // Opsional: catat ke log jika admin tidak ditemukan
            // \Log::error('Admin dengan email annisarne12@gmail.com tidak ditemukan untuk notifikasi.');
        }

        // Jika Anda ingin menghapus notifikasi email, hapus atau beri komentar pada baris di bawah ini
        // $emailadmin->notify(new NotifikasiPermintaanBarang($pesannama, $pesanbarang, $pesanstok));


        return redirect()->route('siminbarpermintaanbarang.getPermintaanBarangUser')->with(['success' => 'Data Berhasil Disimpan!']);
    }


    private function sendWhatsAppPermintaanBarang($admin, $namaPeminta, $namaBarang, $jumlahStok, $catatan, $tanggalPermintaan)
    {
        // Ganti dengan token Fonnte Anda. Sebaiknya simpan di file .env untuk keamanan.
        $token = env('FONNTE_TOKEN');

        // Pastikan model User Anda memiliki kolom no_hp atau sesuaikan nama kolomnya
        $targetPhoneNumber = $admin->nomer_telepon;

        // Jika nomor telepon tidak ada, hentikan fungsi
        if (!$targetPhoneNumber) {
            // Anda bisa menambahkan log di sini jika perlu
            // \Log::warning('Nomor HP admin tidak ditemukan untuk notifikasi WhatsApp.');
            return;
        }

        // 1. Atur bahasa ke Indonesia untuk nama bulan
        Carbon::setLocale('id');

        // 2. Ubah format tanggal yang diterima dari parameter
        $tanggalFormatted = Carbon::parse($tanggalPermintaan)->translatedFormat('d F Y');


        // Membuat pesan notifikasi
        $message = "🔔 *Notifikasi Permintaan Barang Baru* 🔔\n\n"
            . "Halo *{$admin->name}*,\n"
            . "Ada permintaan barang baru yang masuk:\n\n"
            . "👤 *Peminta:* {$namaPeminta}\n"
            . "📦 *Nama Barang:* {$namaBarang}\n"
            . "🔢 *Jumlah:* {$jumlahStok} unit\n";

        // Tambahkan bagian catatan HANYA JIKA catatan diisi oleh pengguna
        if (!empty($catatan)) {
            $message .= "📝 *Catatan:* {$catatan}\n";
        }

        // Tambahkan Tanggal Order SETELAH pengecekan catatan
        $message .= "🗓️ *Tanggal Order:* {$tanggalFormatted}\n";

        $message .= "\nMohon untuk segera ditindaklanjuti.";

        // Mengirim request ke API Fonnte menggunakan cURL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $targetPhoneNumber,
                'message' => $message,
                'countryCode' => '62', // Opsional, sesuaikan jika perlu
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Anda bisa menyimpan response dari Fonnte ke log untuk debugging
        // \Log::info('Fonnte API Response: ' . $response);
    }


    public function getPermintaanBarangAdminByID($id)
    {
        // Mengambil semua data barang dari database
        $permintaanbarang = PermintaanBarang::findOrFail($id);

        // Debugging jika ingin melihat data sebelum transform
        // dd($barangs); // Uncomment jika ingin melihat hasil query sebelum transform

        // Mengubah data barang sesuai dengan yang diperlukan
        $barangs = $permintaanbarang->barang_id;
        $barang = Barang::findOrFail($barangs);
        $namabarang = $barang->namabarang;
        $users = $permintaanbarang->user_id;
        $user = User::findOrFail($users);
        $namauser = $user->name;
        $permintaanbarangData = [
            'barang_id' => $permintaanbarang->barang_id,
            'namabarang' => $namabarang,
            'user_id' => $permintaanbarang->user_id,
            'name' => $namauser,
            'status' => $permintaanbarang->status,
            'catatan' => $permintaanbarang->catatan,
            'stokpermintaan' => $permintaanbarang->stokpermintaan,
            'ttduser' => $permintaanbarang->ttduser,
            'ttdadmin' => $permintaanbarang->ttdadmin,
            'ttdumum' => $permintaanbarang->ttdumum,
            'orderdate' => $permintaanbarang->orderdate,
            'stoktersedia' => $barang->stoktersedia,
            'buktifoto' => $permintaanbarang->buktifoto,
            'id' => $permintaanbarang->id,
        ];

        // dd($permintaanbarangData);

        return response()->json($permintaanbarangData);
    }

    public function getPermintaanBarangAdminForm($id)
    {
        // Ambil satu item permintaan barang berdasarkan ID
        $permintaanbarang = PermintaanBarang::findOrFail($id);

        // Ambil barang berdasarkan barang_id dari permintaan
        $barang = Barang::findOrFail($permintaanbarang->barang_id);
        $namabarang = $barang->namabarang;

        // Ambil user berdasarkan user_id dari permintaan
        $user = User::findOrFail($permintaanbarang->user_id);
        $namauser = $user->name;

        // Susun data untuk dikirim ke view
        $permintaanbarangData = [
            'barang_id' => $permintaanbarang->barang_id,
            'namabarang' => $namabarang,
            'user_id' => $permintaanbarang->user_id,
            'namauser' => $namauser,
            'status' => $permintaanbarang->status,
            'catatan' => $permintaanbarang->catatan,
            'orderdate' => $permintaanbarang->orderdate,
            'stokpermintaan' => $permintaanbarang->stokpermintaan,
            'ttduser' => $permintaanbarang->ttduser,
            'stoktersedia' => $barang->stoktersedia,
            'buktifoto' => $permintaanbarang->buktifoto,
            'id' => $permintaanbarang->id,
        ];

        // Kembalikan data ke view
        return view('siminbar.siminbarpermintaanbarangadminform', ['permintaanbarang' => $permintaanbarangData]);
    }

    public function getPermintaanBarangAdmin(Request $request)
    {
        // Set the number of items per page
        $perPage = 25; // Change this number as needed

        // Retrieve the start and end dates from the request
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $searchKeyword = $request->input('search'); // Ambil kata kunci pencarian

        // Initialize the query with join
        $query = PermintaanBarang::with(['barang', 'user']); // Pastikan Anda memiliki relasi yang tepat di model

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('orderdate', '>=', $startDate)
                ->where('orderdate', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->whereHas('barang', function ($query) use ($searchKeyword) {
                    $query->where('namabarang', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan namabarang
                })
                    ->orWhereHas('user', function ($query) use ($searchKeyword) {
                        $query->where('name', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama user
                    });
            });
        }

        // Execute the query, paginate, and retrieve the results
        $permintaanbarangs = $query->orderBy('orderdate', 'desc')->paginate($perPage);


        // Transform the results as needed
        $permintaanbarangs->getCollection()->transform(function ($permintaanbarang) {
            $barang = $permintaanbarang->barang; // Mengambil data barang dari relasi
            $user = $permintaanbarang->user; // Mengambil data user dari relasi

            return [
                'barang_id' => $permintaanbarang->barang_id,
                'namabarang' => $barang->namabarang ?? 'N/A', // Menampilkan namabarang
                'user_id' => $permintaanbarang->user_id,
                'namauser' => $user->name ?? 'N/A', // Menampilkan nama user
                'status' => $permintaanbarang->status,
                'catatan' => $permintaanbarang->catatan,
                'orderdate' => $permintaanbarang->orderdate,
                'stokpermintaan' => $permintaanbarang->stokpermintaan,
                'ttduser' => $permintaanbarang->ttduser,
                'stoktersedia' => $permintaanbarang->stoktersedia,
                'buktifoto' => $permintaanbarang->buktifoto,
                'id' => $permintaanbarang->id,
            ];
        });

        $topBarangs = DB::table('permintaanbarangs')
            ->join('barangs', 'permintaanbarangs.barang_id', '=', 'barangs.id')
            ->select('barangs.*', DB::raw('SUM(permintaanbarangs.stokpermintaan) as total_stokpermintaan'))
            ->groupBy('permintaanbarangs.barang_id', 'barangs.id')
            ->orderBy('total_stokpermintaan', 'desc')
            ->limit(10)
            ->get();

        // Transform the result
        $topBarangs = $topBarangs->map(function ($topBarang) {
            return [ // yang ada di tampilan tabel
                'namabarang' => $topBarang->namabarang,
                'stoktersedia' => $topBarang->stoktersedia,
                'gambar' => $topBarang->gambar,
                'deskripsi' => $topBarang->deskripsi,
                'id' => $topBarang->id,
            ];
        });

        // dd($topBarang);
        return view('siminbar.siminbarpermintaanbarangadmin', ['permintaanbarangs' => $permintaanbarangs, 'topBarangs' => $topBarangs]);
    }

    public function storeAdmin(Request $request, $id): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }
        $permintaanbarang = PermintaanBarang::findOrFail($id);
        // dd($request);
        if ($request->input('ttdadmin')) {
            // Mengonversi data URL tanda tangan ke dalam format gambar
            $signatureData = $request->input('ttdadmin');
            $image = str_replace('data:image/png;base64,', '', $signatureData);
            $image = str_replace(' ', '+', $image); // Mengganti spasi dengan plus
            $imageName = time() . '.png'; // Nama file untuk tanda tangan dengan format waktu

            // Simpan gambar tanda tangan ke dalam folder storage/public/signatures
            Storage::disk('public')->put('uploads/signatures/' . $imageName, base64_decode($image));

            // $signature_path = 'uploads/signatures/' . $imageName; // Simpan path gambar tanda tangan
            $signature_path = $imageName;
            $status = 'approved';
        } else {
            $signature_path = null;
            $status = 'disapproved';
        };

        // create product
        $permintaanbarang->update([
            'ttdadmin'         => $signature_path,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        try {
            if ($status == 'approved') {
                $barang = Barang::findOrFail($request->barang_id);
                $barang->update([
                    'stoktersedia' => $barang->stoktersedia - $permintaanbarang->stokpermintaan,
                ]);
            } else {
                $barang = Barang::findOrFail($request->barang_id);
                $barang->update([
                    'stoktersedia' => $barang->stoktersedia,
                ]);
            }
        } catch (\Exception $e) {
            // Log error untuk debugging
            // Log::error('Error updating stock: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui stok.']);
        }



        // Log::info('Redirecting to the next page...');
        return redirect()->route('siminbarpermintaanbarangadmin.getPermintaanBarangAdmin')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function getPermintaanBarangUmumByID($id)
    {
        // Mengambil semua data barang dari database
        $permintaanbarang = PermintaanBarang::findOrFail($id);

        // Jika tidak ada data barang
        if ($permintaanbarang->isEmpty()) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        // Debugging jika ingin melihat data sebelum transform
        // dd($barangs); // Uncomment jika ingin melihat hasil query sebelum transform

        // Mengubah data barang sesuai dengan yang diperlukan
        $barangs = $permintaanbarang->barang_id;
        $barang = Barang::findOrFail($barangs);
        $namabarang = $barang->namabarang;
        $users = $permintaanbarang->user_id;
        $user = User::findOrFail($users);
        $namauser = $user->name;
        $permintaanbarangData = [
            'barang_id' => $permintaanbarang->barang_id,
            'namabarang' => $namabarang,
            'user_id' => $permintaanbarang->user_id,
            'name' => $namauser,
            'status' => $permintaanbarang->status,
            'catatan' => $permintaanbarang->catatan,
            'stokpermintaan' => $permintaanbarang->stokpermintaan,
            'ttduser' => $permintaanbarang->ttduser,
            'ttdadmin' => $permintaanbarang->ttdadmin,
            'orderdate' => $permintaanbarang->orderdate,
            'stoktersedia' => $barang->stoktersedia,
            'buktifoto' => $permintaanbarang->buktifoto,
            'id' => $permintaanbarang->id,
        ];

        // dd($permintaanbarangData);

        return response()->json($permintaanbarangData);
    }
    public function getPermintaanBarangUmumForm($id)
    {
        // Ambil permintaan barang beserta barang dan user-nya
        $permintaanbarang = PermintaanBarang::with(['barang', 'user'])->findOrFail($id);

        // Ambil nama barang, stok tersedia dari relasi barang, dan nama user
        $namabarang = $permintaanbarang->barang->namabarang;
        $stoktersedia = $permintaanbarang->barang->stoktersedia;  // Ambil stok tersedia dari tabel barang
        $namauser = $permintaanbarang->user->name;

        // Persiapkan data yang akan dikirim ke view
        $permintaanbarangdata = [
            'barang_id' => $permintaanbarang->barang_id,
            'namabarang' => $namabarang,
            'user_id' => $permintaanbarang->user_id,
            'namauser' => $namauser,
            'status' => $permintaanbarang->status,
            'catatan' => $permintaanbarang->catatan,
            'orderdate' => $permintaanbarang->orderdate,
            'stokpermintaan' => $permintaanbarang->stokpermintaan,
            'ttduser' => $permintaanbarang->ttduser,
            'ttdadmin' => $permintaanbarang->ttdadmin,
            'stoktersedia' => $stoktersedia,  // Ambil stok tersedia dari tabel barang
            'buktifoto' => $permintaanbarang->buktifoto,
            'id' => $permintaanbarang->id,
        ];

        // dd($permintaanbarangdata);

        // Kirim data ke view
        return view('siminbar.siminbarpermintaanbarangumumform', ['permintaanbarang' => $permintaanbarangdata]);
    }
    public function getPermintaanBarangUmum(Request $request)
    {

        // Set the number of items per page
        $perPage = 25; // Change this number as needed

        // // Retrieve the start and end dates from the request
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $searchKeyword = $request->input('search'); // Ambil kata kunci pencarian

        // // Initialize the query
        $query = PermintaanBarang::query();

        // // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('orderdate', '>=', $startDate)
                ->where('orderdate', '<=', $endDate);
        }

        // // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('namabarang', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('user', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }

        // // Execute the query, paginate, and retrieve the results
        $permintaanbarangs = $query->orderBy('orderdate', 'desc')->paginate($perPage);


        // // Map the results to transform the 'absen' format
        // $suratmasuks->getCollection()->transform(function ($suratmasuk) {
        //     $disposisiIds = SuratMasukDisposisi::where('suratmasuk_id', $suratmasuk->id)->pluck('disposisi_id');
        //     $nama_disposisi = Disposisi::whereIn('id', $disposisiIds)->pluck('namadisposisi')->toArray();
        //     // Gabungkan semua nama disposisi menjadi string
        //     $nama_disposisi_string = implode(', ', $nama_disposisi);
        //     return [ // yg ada di tampilan tabel
        //         'perihal' => $suratmasuk->perihal,
        //         'tglterima' => Carbon::parse($suratmasuk->tglterima)->translatedFormat('d F Y H:i:s'),
        //         'namadisposisi' => $nama_disposisi_string,
        //         'namainstansi' => $suratmasuk->namainstansi,
        //         'nosurat' => $suratmasuk->nosurat,
        //         'id' => $suratmasuk->id,
        //     ];
        // });
        // Mengambil semua data barang dari database
        // $permintaanbarangs = PermintaanBarang::all();

        // // Jika tidak ada data barang
        // if ($permintaanbarangs->isEmpty()) {
        //     return response()->json(['message' => 'Barang not found'], 404);
        // }

        // Debugging jika ingin melihat data sebelum transform
        // dd($barangs); // Uncomment jika ingin melihat hasil query sebelum transform

        // Mengubah data barang sesuai dengan yang diperlukan
        $permintaanbarangs->getCollection()->transform(function ($permintaanbarang) {
            $barangs = $permintaanbarang->barang_id;
            $barang = Barang::findOrFail($barangs);
            $namabarang = $barang->namabarang;
            $users = $permintaanbarang->user_id;
            $user = User::findOrFail($users);
            $namauser = $user->name;
            return [
                'barang_id' => $permintaanbarang->barang_id,
                'namabarang' => $namabarang,
                'user_id' => $permintaanbarang->user_id,
                'namauser' => $namauser,
                'status' => $permintaanbarang->status,
                'catatan' => $permintaanbarang->catatan,
                'orderdate' => $permintaanbarang->orderdate,
                'stokpermintaan' => $permintaanbarang->stokpermintaan,
                'ttduser' => $permintaanbarang->ttduser,
                'ttdadmin' => $permintaanbarang->ttdadmin,
                'stoktersedia' => $permintaanbarang->stoktersedia,
                'buktifoto' => $permintaanbarang->buktifoto,
                'id' => $permintaanbarang->id,
            ];
        });

        // dd($barangs);

        return view('siminbar.siminbarpermintaanbarangumum', ['permintaanbarangs' => $permintaanbarangs]);
    }

    public function storeUmum(Request $request, $id): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }
        $permintaanbarang = PermintaanBarang::findOrFail($id);
        // dd($request);
        // Mengonversi data URL tanda tangan ke dalam format gambar
        $signatureData = $request->input('ttdumum');
        $image = str_replace('data:image/png;base64,', '', $signatureData);
        $image = str_replace(' ', '+', $image); // Mengganti spasi dengan plus
        $imageName = time() . '.png'; // Nama file untuk tanda tangan dengan format waktu

        // Simpan gambar tanda tangan ke dalam folder storage/public/signatures
        Storage::disk('public')->put('uploads/signatures/' . $imageName, base64_decode($image));

        // $signature_path = 'uploads/signatures/' . $imageName; // Simpan path gambar tanda tangan
        $signature_path = $imageName;

        // create product
        $permintaanbarang->update([
            'ttdumum'         => $signature_path,
        ]);

        return redirect()->route('siminbarpermintaanbarangadmin.getPermintaanBarangAdmin')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function getPermintaanBarangUser(Request $request)
    {
        // Set the number of items per page
        $perPage = 25; // Change this number as needed

        // Retrieve the start and end dates from the request
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $searchKeyword = $request->input('search'); // Ambil kata kunci pencarian

        $namauser = Auth()->user()->id;
        // Initialize the query with join
        $query = PermintaanBarang::with(['barang', 'user'])->where('user_id', $namauser); // Pastikan Anda memiliki relasi yang tepat di model

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('orderdate', '>=', $startDate)
                ->where('orderdate', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->whereHas('barang', function ($query) use ($searchKeyword) {
                    $query->where('namabarang', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan namabarang
                });
            });
        }

        // Execute the query, paginate, and retrieve the results
        $permintaanbarangs = $query->orderBy('orderdate', 'desc')->paginate($perPage);

        // Transform the results as needed
        $permintaanbarangs->getCollection()->transform(function ($permintaanbarang) {
            $barang = $permintaanbarang->barang; // Mengambil data barang dari relasi
            $user = $permintaanbarang->user; // Mengambil data user dari relasi

            return [
                'barang_id' => $permintaanbarang->barang_id,
                'namabarang' => $barang->namabarang ?? 'N/A', // Menampilkan namabarang
                'user_id' => $permintaanbarang->user_id,
                'namauser' => $user->name ?? 'N/A', // Menampilkan nama user
                'status' => $permintaanbarang->status,
                'catatan' => $permintaanbarang->catatan,
                'orderdate' => $permintaanbarang->orderdate,
                'stokpermintaan' => $permintaanbarang->stokpermintaan,
                'ttduser' => $permintaanbarang->ttduser,
                'stoktersedia' => $permintaanbarang->stoktersedia,
                'buktifoto' => $permintaanbarang->buktifoto,
                'id' => $permintaanbarang->id,
            ];
        });

        $topBarangs = DB::table('permintaanbarangs')
            ->join('barangs', 'permintaanbarangs.barang_id', '=', 'barangs.id')
            ->select('barangs.*', DB::raw('SUM(permintaanbarangs.stokpermintaan) as total_stokpermintaan'))
            ->groupBy('permintaanbarangs.barang_id', 'barangs.id')
            ->orderBy('total_stokpermintaan', 'desc')
            ->limit(10)
            ->get();

        // Transform the result
        $topBarangs = $topBarangs->map(function ($topBarang) {
            return [ // yang ada di tampilan tabel
                'namabarang' => $topBarang->namabarang,
                'stoktersedia' => $topBarang->stoktersedia,
                'gambar' => $topBarang->gambar,
                'deskripsi' => $topBarang->deskripsi,
                'id' => $topBarang->id,
            ];
        });

        // dd($topBarangs);

        return view('siminbar.siminbarpermintaanbarang', ['permintaanbarangs' => $permintaanbarangs, 'topBarangs' => $topBarangs]);
    }

    public function getPermintaanBarangUserByID($id)
    {
        // Mengambil semua data barang dari database
        $permintaanbarang = PermintaanBarang::findOrFail($id);

        // Debugging jika ingin melihat data sebelum transform
        // dd($barangs); // Uncomment jika ingin melihat hasil query sebelum transform

        // Mengubah data barang sesuai dengan yang diperlukan
        $barangs = $permintaanbarang->barang_id;
        $barang = Barang::findOrFail($barangs);
        $namabarang = $barang->namabarang;
        $users = $permintaanbarang->user_id;
        $user = User::findOrFail($users);
        $namauser = $user->name;
        $permintaanbarangData = [
            'barang_id' => $permintaanbarang->barang_id,
            'namabarang' => $namabarang,
            'user_id' => $permintaanbarang->user_id,
            'name' => $namauser,
            'status' => $permintaanbarang->status,
            'catatan' => $permintaanbarang->catatan,
            'stokpermintaan' => $permintaanbarang->stokpermintaan,
            'ttduser' => $permintaanbarang->ttduser,
            'ttdadmin' => $permintaanbarang->ttdadmin,
            'ttdumum' => $permintaanbarang->ttdumum,
            'orderdate' => $permintaanbarang->orderdate,
            'stoktersedia' => $barang->stoktersedia,
            'buktifoto' => $permintaanbarang->buktifoto,
            'id' => $permintaanbarang->id,
        ];

        // dd($permintaanbarangData);

        return response()->json($permintaanbarangData);
    }

    public function pdf_export_get($id)
    {
        // dd($request);

        $permintaanbarang = PermintaanBarang::findOrFail($id);

        // Mengubah data barang sesuai dengan yang diperlukan
        $barangs = $permintaanbarang->barang_id;
        $barang = Barang::findOrFail($barangs);
        $namabarang = $barang->namabarang;
        $users = $permintaanbarang->user_id;
        $user = User::findOrFail($users);
        $namauser = $user->name;
        // $path_gambar = [
        //     public_path('storage/app/public/uploads/signatures/' . $permintaanbarang->ttduser),
        //     public_path('storage/app/public/uploads/signatures/' . $permintaanbarang->ttdadmin),
        //     public_path('storage/app/public/uploads/signatures/' . $permintaanbarang->ttdumum),
        //     public_path('storage/app/public/uploads/images/' . $permintaanbarang->buktifoto),
        // ];
        $permintaanbarangData = [
            'barang_id' => $permintaanbarang->barang_id,
            'namabarang' => $namabarang,
            'user_id' => $permintaanbarang->user_id,
            'name' => $namauser,
            'status' => $permintaanbarang->status,
            'catatan' => $permintaanbarang->catatan,
            'stokpermintaan' => $permintaanbarang->stokpermintaan,
            'ttduser' => $permintaanbarang->ttduser,
            'ttdadmin' => $permintaanbarang->ttdadmin,
            'ttdumum' => $permintaanbarang->ttdumum,
            'orderdate' => $permintaanbarang->orderdate,
            'stoktersedia' => $barang->stoktersedia,
            'buktifoto' => $permintaanbarang->buktifoto,
            'id' => $permintaanbarang->id,
        ];

        $tanggaldownload = Carbon::now()->format('d_m_Y'); // Ganti "-" dengan "_"

        // Bersihkan nama user dan nama barang dari karakter tidak valid
        $namauser = preg_replace('/[^A-Za-z0-9_]/', '_', $namauser);
        $namabarang = preg_replace('/[^A-Za-z0-9_]/', '_', $namabarang);

        $filename = strtolower($namauser . '_Laporan_Permintaan_' . $namabarang . '_' . $tanggaldownload . '.pdf');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('siminbar.laporanbarang', compact('permintaanbarangData'))->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    public function delete($id)
    {
        $suratmasuk = PermintaanBarang::findOrFail($id);
        $suratmasuk->delete();

        // return redirect('agenkitaagenda.getEvents')->with('success', 'Presensi berhasil dihapus.');
        return redirect('/siminbardaftarbarang')->with('success', 'Permintaan barang berhasil dihapus.');
    }

    public function pdf_export_get_search(Request $request)
    {
        // dd($request);
        // Mengambil nilai dari query string
        $searchKeyword = $request->query('search'); // atau $request->input('search');
        $startDateInput = $request->query('start_date'); // atau $request->input('start');
        $endDateInput = $request->query('end_date'); // atau $request->input('end');

        // Initialize the query
        $query = PermintaanBarang::query();

        // Convert input dates to Carbon instances in the correct format
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::createFromFormat('m/d/Y', $startDateInput)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDateInput)->endOfDay();

            // Filter the query based on the provided startDate and endDate
            $query->where('orderdate', '>=', $startDate)
                ->where('orderdate', '<=', $endDate);
        }

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->whereHas('barang', function ($query) use ($searchKeyword) {
                    $query->where('namabarang', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan namabarang
                })
                    ->orWhereHas('user', function ($query) use ($searchKeyword) {
                        $query->where('name', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama user
                    });
            });
        }

        // Execute the query, paginate, and retrieve the results
        // $presences = $query->orderBy('absen', 'desc')->get();
        $permintaanbarangs = $query->orderBy('orderdate', 'desc')->get();

        // dd($actt);
        $datas = [];
        foreach ($permintaanbarangs as $permintaanbarang) {
            $datas[] = [
                $barangs = $permintaanbarang->barang_id,
                $barang = Barang::findOrFail($barangs),
                $namabarang = $barang->namabarang,
                $users = $permintaanbarang->user_id,
                $user = User::findOrFail($users),
                $namauser = $user->name,
                'barang_id' => $permintaanbarang->barang_id,
                'namabarang' => $namabarang,
                'user_id' => $permintaanbarang->user_id,
                'name' => $namauser,
                'status' => $permintaanbarang->status,
                'catatan' => $permintaanbarang->catatan,
                'stokpermintaan' => $permintaanbarang->stokpermintaan,
                'orderdate' => $permintaanbarang->orderdate,
                'stoktersedia' => $barang->stoktersedia,
                'id' => $permintaanbarang->id,
            ];
        }
        // dd($datas);
        $tanggaldownload = Carbon::now()->format('d-m-Y');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('siminbar.laporansiminbar', ['datas' => $datas])->setPaper('a4', 'landscape');
        $filename = 'Laporan Permintaan Barang_' . $tanggaldownload . '.pdf';
        return $pdf->download($filename);
    }

    // public function getTopBarang(){
    //     $topBarang = DB::table('permintaanbarangs')
    //         ->join('barangs', 'permintaanbarangs.id_barang', '=', 'barangs.id')
    //         ->select('barangs.*', DB::raw('SUM(permintaanbarangs.stokpermintaan) as total_stokpermintaan'))
    //         ->groupBy('permintaanbarangs.id_barang', 'barangs.id')
    //         ->orderBy('total_stokpermintaan', 'desc')
    //         ->limit(12)
    //         ->get();

    //     // Return hasil query dalam bentuk JSON atau kirim ke view
    //     return response()->json($topBarang);
    // }
}