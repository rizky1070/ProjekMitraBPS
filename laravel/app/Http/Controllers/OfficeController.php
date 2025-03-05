<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Category;
use Auth;
use Session;
use Illuminate\Http\Request;

class OfficeController extends Controller
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
                $query = Office::query();
        
                // Menerapkan pencarian jika ada
                if ($request->filled('search')) {
                    $query->where('name', 'like', '%' . request('search') . '%');
                }
        
                // Menentukan jumlah baris per halaman
                $rowsPerPage = $request->input('rows', 10);
        
                // Mengurutkan data berdasarkan nama (name)
                $query->orderBy('status', 'DESC')->orderBy('name', 'ASC');
        
                // Mengambil data dengan pagination
                $office = $query->paginate($rowsPerPage);
        
                return view('page.admin.office.index', compact('office'));
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
        $category = Category::all();
        return view('page.admin.office.create', compact('category'));
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
		    return redirect()->route('office.create');
        } else {
            $create = Office::create($data);
        }
        return redirect()->route('office.index');
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
        $office = Office::findOrFail($id);
        $category = Category::all();

        return view('page.admin.office.edit', compact('office', 'category'));
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
        $office = Office::findOrFail($id);
        
        $update = $office->update([
            'link' => $request->link,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'status' => $request->status
        ]);

        return redirect()->route('office.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $office = Office::findOrFail($id);

        $delete = $office->delete();

        return redirect()->route('office.index');
    }
}
