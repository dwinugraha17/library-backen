@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Buku</h3>
        <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card card-custom p-4">
        <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="title" class="form-control" value="{{ $book->title }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="author" class="form-control" value="{{ $book->author }}" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select" required>
                        @foreach(['Teknologi', 'Novel', 'Bisnis', 'Sains', 'Sejarah', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ $book->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stock" class="form-control" min="0" value="{{ $book->stock }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Ganti Cover (Opsional)</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                    <div class="mt-2">
                        <small class="text-muted">Cover saat ini:</small>
                        <br>
                        <img src="{{ $book->cover_image }}" alt="Current Cover" width="80" class="rounded border mt-1">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ $book->description }}</textarea>
                </div>

                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-warning px-4 text-white">
                        <i class="fas fa-save me-1"></i> Perbarui Buku
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
