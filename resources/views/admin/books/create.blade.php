@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Tambah Buku Baru</h3>
        <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card card-custom p-4">
        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="author" class="form-control" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select" required>
                        <option value="Teknologi">Teknologi</option>
                        <option value="Novel">Novel</option>
                        <option value="Bisnis">Bisnis</option>
                        <option value="Sains">Sains</option>
                        <option value="Sejarah">Sejarah</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stock" class="form-control" min="0" value="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cover Image</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                </div>

                <div class="col-md-12">
                    <label class="form-label">File PDF (Opsional)</label>
                    <input type="file" name="book_file" class="form-control" accept=".pdf">
                </div>

                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Buku
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
