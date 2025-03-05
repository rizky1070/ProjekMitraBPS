<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\Ketua;
use App\Models\Category;
use Illuminate\Http\Request;

class KetuaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
{
    $id = Auth::id();
    if (Auth::user()->roles == 'ADMIN') {
        $query = Ketua::query();

        // Menerapkan pencarian jika ada
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        // Menentukan jumlah baris per halaman
        $rowsPerPage = $request->input('rows', 10);

        // Mengurutkan data berdasarkan nama (name)
        $query->orderBy('status', 'DESC')->orderBy('name', 'ASC');

        // Mengambil data dengan pagination
        $ketua = $query->paginate($rowsPerPage);

        return view('page.admin.ketua.index', compact('ketua'));
    } else {
        return redirect('/');
    }
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user = User::where('roles', 'ADMIN')->orWhere('roles', 'KETUA')->get();
        $category = Category::all();
        return view('page.admin.ketua.create', compact('category'));
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

        if (empty($request->link)) {
            Session::flash('gagal','Link tidak boleh kosong');
		    return redirect()->route('sekretariat.create');
        } else {
            $create = Ketua::create($data);
        }
        return redirect()->route('sekretariat.index');
        
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
        $ketua = Ketua::findOrFail($id);
        $category = Category::all();

        return view('page.admin.ketua.edit', compact('ketua', 'category'));
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
        $ketua = Ketua::findOrFail($id);
        
        $update = $ketua->update([
            'name' => $request->name,
            'link' => $request->link,
            'category_id' => $request->category_id,
            'status' => $request->status
        ]);

        if ($update) {
            return redirect()->route('sekretariat.index');
        } else {
            dd($error);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ketua = Ketua::findOrFail($id);

        $delete = $ketua->delete();

        return redirect()->route('sekretariat.index');
    }
}
