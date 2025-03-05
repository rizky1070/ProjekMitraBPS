<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Session;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->roles == 'ADMIN') {
            if($request->filled('search')){
                $user = User::where('name', 'LIKE', '%' . $request->search . '%')
                             ->orWhere('roles', 'LIKE', '%' . $request->search . '%')
                             ->orWhere('username', 'LIKE', '%' . $request->search . '%')
                             ->get();
            }else{
                if ($request->filled('showAll')) {
                    $user = User::orderBy('roles', 'ASC')->orderBy('name', 'ASC')->get();
                } else {
                    $user = User::orderBy('roles', 'ASC')->orderBy('name', 'ASC')->paginate(15);
                }
                
            }
            return view('page.admin.users.index', compact('user'));
        } else {
            return redirect('/linkuser');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('page.admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        User::create($data);
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::findOrFail($id);
        $roles = [
            'ADMIN',
            'USER',
            'SEKRETARIAT'
        ];

        return view('page.admin.users.edit', compact('users', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $users = User::findOrFail($id);
        
        $usernameExists = User::where('username', $request->username)->first();

        if ($usernameExists) {
            if ($usernameExists != null) {
                Session::flash('gagal','username yang anda masukkan telah terdaftar!');
                return redirect()->route('users.edit', $id);
            } else {
                if (empty($request->password)) {
                    $update = $users->update([
                        'name' => $request->name,
                        'username' => $request->username,
                        'roles' => $request->roles
                    ]);
                } else {
                    $crypt = bcrypt($request->password);
                    $update = $users->update([
                        'name' => $request->name,
                        'username' => $request->username,
                        'password' => $crypt,
                        'roles' => $request->roles,
                    ]);
                }
            }
        } else {
            if (empty($request->password)) {
                $update = $users->update([
                    'name' => $request->name,
                    'username' => $request->username,
                    'roles' => $request->roles
                ]);
            } else {
                $crypt = bcrypt($request->password);
                $update = $users->update([
                    'name' => $request->name,
                    'username' => $request->username,
                    'password' => $crypt,
                    'roles' => $request->roles,
                ]);
            }
        }
        
        return redirect()->route('users.index');
    }
    public function updateuser(Request $request, $id)
{
    $users = User::findOrFail($id);
    
    $usernameExists = User::where('username', $request->username)
                            ->where('id', '!=', $id)
                            ->first();

    if ($id != Auth::id()) {
        if ($usernameExists) {
            Session::flash('gagal', 'Username yang Anda masukkan telah terdaftar!');
            return redirect()->route('edit-user');
        }
    }

    $updateData = [
        'name' => $request->name,
        'username' => $request->username,
        'roles' => $request->roles
    ];

    if (!empty($request->password)) {
        $crypt = bcrypt($request->password);
        $updateData['password'] = $crypt;
    }

    $update = $users->update($updateData);

    return redirect()->route('link-user');
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = User::findOrFail($id);

        $delete = $users->delete();
        return redirect()->route('users.index');
    }
}
