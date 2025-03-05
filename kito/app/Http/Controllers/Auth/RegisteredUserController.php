<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Storage;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Cek keunikan 'namasingkat'
        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'Email sudah ada, silakan gunakan yang lain.']);
        }
        if (User::where('username', $request->username)->exists()) {
            return redirect()->back()->withErrors(['username' => 'Username sudah ada, silakan gunakan yang lain.']);
        }

        if ($request->input('gambar') != null) {
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
            $image_name = null;
        }
        // dd($request);
        // $request->validate([
        //     // 'name' => ['required', 'string', 'max:255'],
        //     // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        //     // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
        //     // 'username' => ['required', 'string', 'lowercase', 'username', 'max:255', 'unique:'.User::class]
        // ]);

        // dd($request);
        $user = User::create([
            'name'         => $request->name,
            'jabatan' => $request->jabatan,
            'email' => $request->email,
            'is_admin' => $request->is_admin,
            'is_leader'  => $request->is_leader,
            'is_hamukti' => $request->is_hamukti,
            'is_active' => $request->is_active,
            'username' => $request->username,
            'gambar'  => $image_name,
            'password' => Hash::make($request->password), // Hash password
        ]);

        // dd($user);

        event(new Registered($user));

        // Auth::login($user);

        // return redirect(RouteServiceProvider::HOME);
        return redirect()->route('daftaruser.index')->with(['success' => 'User Berhasil Ditambahkan!']);
    }
}
