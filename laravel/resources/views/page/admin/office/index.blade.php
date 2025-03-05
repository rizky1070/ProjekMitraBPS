@extends('layouts.dashboard')

@section('title')
    Link Super Tim
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">SUPER TIM</h1>
    <a href="{{ route('link-kantor') }}" class="btn btn-primary">Home</a>
    <a href="{{ route('office.create') }}" class="btn btn-primary">Tambah Link</a>
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
                aria-describedby="button-addon2"
            >
            <button class="btn btn-success mt-3" type="submit" id="button-addon2">Search</button>
        </div>
    </form>
    <div class="table-responsive">
        @if ($office->count())
            <table class="table table-bordered">
                <thead>
                    <tr style="background-color: #04B431; font-weight: bold;color: white;">
                        <th class="text-center">No</th>
                        <th class="text-center">Judul Link</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Ditampilkan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $count = ($office->currentPage() - 1) * $office->perPage(); @endphp
                    @foreach ($office as $row)
                        <tr id="row-{{ $row->id }}">
                            <td class="text-center" id="row">{{ ++$count }}.</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->category->name }}</td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-{{ $row->status ? 'success' : 'danger' }}">
                                    {{ $row->status ? 'Ya' : 'Tidak' }}
                                </a>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('office.edit', $row->id) }}" class="d-inline">
                                    @method('PUT')
                                    <button class="btn btn-warning">Edit</button>
                                </form> |
                                <form action="{{ route('office.destroy', $row->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Info and Dropdown Rows Per Page -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="pagination-info">
                    {{ $office->firstItem() }}-{{ $office->lastItem() }} dari {{ $office->total() }} Link
                </div>
                <div class="rows-per-page">
                    <label for="rowsPerPage">Link per Halaman:</label>
                    <select id="rowsPerPage" onchange="changeRowsPerPage()">
                        <option value="10" {{ ($office->perPage() == 10) ? 'selected' : '' }}>10</option>
                        <option value="20" {{ ($office->perPage() == 20) ? 'selected' : '' }}>20</option>
                         <option value="30" {{ ($office->perPage() == 30) ? 'selected' : '' }}>30</option>
                          <option value="100" {{ ($office->perPage() == 100) ? 'selected' : '' }}>100</option>
                        <!-- Tambahkan pilihan lainnya sesuai kebutuhan -->
                    </select>
                </div>
            </div>

            <!-- Pagination Links -->
            {{ $office->appends(request()->except('page'))->links() }}

        @else
            <h1 class="text-center mt-5">Data tidak ditemukan</h1>
        @endif
    </div>
</div>
<script>
    // Fungsi untuk mengubah jumlah baris per halaman saat dropdown dipilih
    function changeRowsPerPage() {
        var selectedValue = document.getElementById('rowsPerPage').value;
        window.location.href = "{{ route('office.index') }}?rows=" + selectedValue;
    }
</script>
@endsection
