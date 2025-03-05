<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }
        User::create([
            'name'         => $request->name,
            'jabatan' => $request->jabatan,
            'email' => $request->email,
            'is_admin' => $request->is_admin,
            'is_leader'  => $request->is_leader,
            'is_hamukti' => $request->is_hamukti,
            'is_active' => $request->is_active,
            'username' => $request->username,
            'password' => Hash::make($request->password), // Hash password
        ]);
        return redirect()->route('daftaruser.index')->with(['success' => 'Data Berhasil Disimpan!']);
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
        $query = User::query();

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('name', 'like', '%' . $searchKeyword . '%') // Mencari berdasarkan kegiatan
                    ->orWhere('jabatan', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan nama
            });
        }

        // Execute the query, paginate, and retrieve the results
        $users = $query->orderBy('name', 'asc')->paginate($perPage);


        // Map the results to transform the 'absen' format
        $users->getCollection()->transform(function ($user) {
            return [ // yg ada di tampilan tabel
                'name' => $user->name,
                'jabatan' => $user->jabatan,
                'email' => $user->email,
                'id' => $user->id,
            ];
        });

        // Return the view with paginated presences
        return view('daftaruser', [
            'users' => $users,
            'pagination' => $users // Pass the paginated data correctly to the view
        ]);
    }

    public function getUserbyId($id)
    {
        $permintaanuser = User::findOrFail($id);
        $permintaanuserData = [
            'name'         => $permintaanuser->name,
            'jabatan' => $permintaanuser->jabatan,
            'email' => $permintaanuser->email,
            'is_admin' => $permintaanuser->is_admin,
            'is_leader'  => $permintaanuser->is_leader,
            'is_hamukti' => $permintaanuser->is_hamukti,
            'is_active' => $permintaanuser->is_active,
            'username' => $permintaanuser->username,
            'id' => $permintaanuser->id,
        ];
        return view('daftaruserformedit', ['user' => $permintaanuserData]);
    }

    public function updatePassword(Request $request, $id)
    {
        // dd($request);
        // Cari user berdasarkan ID
        $user = User::find($id);
        $user->update([

            'password' => Hash::make($request->password),
        ]);
        return redirect()->back()->with(['success' => 'Password Berhasil Diupdate!'], 200);
    }

    public function updateProfile(Request $request, $id)
    {
        // dd($request);
        // Cari user berdasarkan ID
        $user = User::find($id);
        if ($request->input('gambar') != null) {
            if ($user->gambar && Storage::exists('public/uploads/images/' . $user->gambar)) {
                Storage::delete('public/uploads/images/' . $user->gambar);
            }
            // Mengonversi data URL tanda tangan ke dalam format gambar
            $gambarData = $request->input('gambar');
            $image = str_replace('data:image/png;base64,', '', $gambarData);
            $image = str_replace(' ', '+', $image); // Mengganti spasi dengan plus
            $imageName = time() . '.png'; // Nama file untuk tanda tangan dengan format waktu

            // Simpan gambar tanda tangan ke dalam folder storage/public/signatures
            Storage::disk('public')->put('uploads/images/' . $imageName, base64_decode($image));

            // $signature_path = 'uploads/signatures/' . $imageName; // Simpan path gambar tanda tangan
            $image_name = $imageName; // Simpan path gambar tanda tangan
            // $image = $request->file('gambar');
            // $imageName->storeAs('public/uploads/images', $gambar_path->hashName());
            // $image_name = $gambar_path->hashName();
        } else {
            $gambarsekarang = $user->gambar;
            $image_name = $gambarsekarang;
        }
        // dd($image_name);
        $user->update([
            'name' => $request->name,
            'gambar' => $image_name,
           
        ]);
        return redirect()->back()->with(['success' => 'Profile Berhasil Diupdate!'], 200);
    }

    public function update(Request $request, $id)
    {
        $userupdate = User::findOrFail($id);
        $userupdate->update([
            'name'         => $request->name,
            'jabatan' => $request->jabatan,
            'is_admin' => $request->is_admin,
            'is_leader'  => $request->is_leader,
            'is_hamukti' => $request->is_hamukti,
            'is_active' => $request->is_active,
            'password' => Hash::make($request->password), // Hash password
        ]);
        return redirect()->route('daftaruser.index')->with(['success' => 'Data Berhasil Diupdate!'], 200);
    }
}
