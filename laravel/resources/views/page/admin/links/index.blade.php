@extends('layouts.dashboard')

@section('title')
    Link
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">PRIBADI</h1>
    <a href="{{ route('link-kantor') }}" class="btn btn-primary">Home</a>
    <a href="{{ route('links.create') }}" class="btn btn-primary">Tambah Link</a>
</div>

<!-- Content Row -->
<div class="container">
    <form method="GET">
        <div class="form-group mb-5">
          <input 
            type="text" 
            name="search" 
            value="{{ request()->get('search') }}" 
            class="form-control" 
            placeholder="Search..." 
            aria-label="Search" 
            aria-describedby="button-addon2">
          <button class="btn btn-success mt-3" type="submit" id="button-addon2">Search</button>
        </div>
    </form>
    <div class="table-responsive">
        @if ($links->count())
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Judul Link</th>
                    <th>Kategori</th>
                    <th>Ditampilkan</th>
                    <th class="text-center">Aksi</th>
                </tr>
                <tbody>
@foreach ($links as $row)
<tr id="row-{{$row->id}}">
<th class="text-center" id="row">{{ $loop->iteration }}.</th>
<th>{{ $row->name }}</th>
<th>{{ $row->category->name }}</th>
<td> <a class="btn btn-sm btn-{{ $row->status ? 'success' : 'danger' }}">
                    {{ $row->status ? 'Ya' : 'Tidak' }}</a> </td>

                        <th class="text-center">
                            <form action="{{ route('links.edit', $row->id) }}" class="d-inline">
                                @method('PUT')
                                <button class="btn btn-primary">
                                    Edit
                                </button>
                            </form> |
                            <form action="{{ route('links.destroy', $row->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">
                                    Hapus
                                </button>

                            </form>
                        </th>
</tr>
@endforeach
</tbody>
            </table>
        @else
            <h1 class="text-center mt-5">Anda belum menginput link pribadi. Hanya orang yang login menggunakan akun ini yang bisa melihat link pribadi yang telah dibuat. Cobalah buat, misal link2 web yang sering diakses dan susah dihafal. Sebelumnya buat kategori pribadi dulu-->input link pribadi. tks</h1>
        @endif
        @if (request()->get('search') == null && empty(request()->get('showAll')))
            {{ $links->links() }}
            <form method="GET">
                <div class="form-group">
                    <input type="hidden" value="showAll" name="showAll">
                    <button class="btn btn-success mt-3" type="submit" id="button-addon2">Show All</button>
                </div>
            </form>
        @elseif (empty(request()->get('search')))
            <form method="GET">
                <div class="form-group">
                    <input type="hidden" value="showAll" name="showAll">
                    <button class="btn btn-success mt-3" type="submit" id="button-addon2">Show All</button>
                </div>
            </form>
        @else
            
        @endif
    </div>
</div>
@endsection