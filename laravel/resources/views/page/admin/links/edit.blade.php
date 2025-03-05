@extends('layouts.dashboard')

@section('title')
    edit admin
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit link</h1>
</div>

<!-- Content Row -->
<div class="page-inner mt--5">
	<div class="row">
		<div class="col-md-12">
			<div class="card full-height">
				<div class="card-header">
					<div class="card-head-row">
						<div class="card-title">Edit link</div>
						<a href="{{ route('links.index') }}" class="btn btn-primary btn-sm ml-auto">Back</a>
					</div>
				</div>
				
				<div class="card-body">
					<form action="{{ route('links.update', $links->id) }}" method="POST" enctype="multipart/form-data">
						@method('PUT')
						@csrf
						<div class="form-group">
							<label for="judul">Judul Link</label>
							<input type="text" value="{{ $links->name }}" class="form-control" name="name">
						</div>
						<div class="form-group">
							<label for="judul">Link Asli</label>
							<input type="text" value="{{ $links->link }}" class="form-control" name="link">
						</div>
						<div class="form-group">
                        <label for="judul">Kategori</label>
                        <select class="form-control" name="category_user_id" id="">
                            @php
                                $sortedCategories = $category->sortBy('name');
                            @endphp
                            @foreach ($sortedCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $links->category_user_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
					<div class="form-group">
                    <label for="">Ditampilkan ?</label>
                    <select class="form-control" id="type" name="status">
                        @php
                            $statuses = [
                                ['value' => 1, 'label' => 'Ya'],
                                ['value' => 0, 'label' => 'Tidak']
                            ];
                        @endphp
                        @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}" {{ $links->status == $status['value'] ? 'selected' : '' }}>
                                {{ $status['label'] }}
                            </option>
                        @endforeach
                    </select>
                    </div>
						<div class="form-group">
                            <button class="btn btn-primary btn-sm" type="submit">Save</button>
                        </div>
					</form>
				</div>
@endsection