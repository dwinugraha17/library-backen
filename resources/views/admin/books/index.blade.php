@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h2 class="h3 fw-bold text-gray-800 mb-0">Manajemen Buku</h2>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Buku
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Cover</th>
                            <th>Judul & Penulis</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-sm-table-cell">Stok</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td class="ps-4">
                                <img src="{{ $book->cover_image }}" alt="Cover" width="40" height="60" class="rounded shadow-sm object-fit-cover">
                            </td>
                            <td>
                                <div class="fw-bold text-truncate" style="max-width: 200px;">{{ $book->title }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 150px;">{{ $book->author }}</small>
                                <!-- Show Category on Mobile only inside this cell -->
                                <div class="d-md-none mt-1">
                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $book->category }}</span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell"><span class="badge bg-secondary">{{ $book->category }}</span></td>
                            <td class="d-none d-sm-table-cell">{{ $book->stock }}</td>
                            <td>
                                <span class="badge {{ $book->status == 'Available' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $book->status }}
                                </span>
                                <!-- Show Stock on Mobile inside this cell -->
                                <div class="d-sm-none small text-muted mt-1">Stok: {{ $book->stock }}</div>
                            </td>
                            <td class="pe-4 text-end">
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
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-book-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada data buku.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($books->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $books->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
