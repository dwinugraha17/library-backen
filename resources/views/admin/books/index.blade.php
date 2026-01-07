@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Buku</h2>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Buku
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-custom p-4">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Cover</th>
                    <th>Judul & Penulis</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                <tr>
                    <td>
                        <img src="{{ $book->cover_image }}" alt="Cover" width="50" class="rounded shadow-sm">
                    </td>
                    <td>
                        <div class="fw-bold">{{ $book->title }}</div>
                        <small class="text-muted">{{ $book->author }}</small>
                    </td>
                    <td><span class="badge bg-secondary">{{ $book->category }}</span></td>
                    <td>{{ $book->stock }}</td>
                    <td>
                        <span class="badge {{ $book->status == 'Available' ? 'bg-success' : 'bg-danger' }}">
                            {{ $book->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.books.edit', $book->id) }}" class="btn btn-sm btn-warning me-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data buku.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
