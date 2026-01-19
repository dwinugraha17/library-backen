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
            <!-- Desktop View (Table) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Cover</th>
                            <th>Judul & Penulis</th>
                            <th>Kategori</th>
                            <th>Stok</th>
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
                                <small class="text-muted">{{ $book->author }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $book->category }}</span></td>
                            <td>{{ $book->stock }}</td>
                            <td>
                                <span class="badge {{ $book->status == 'Available' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $book->status }}
                                </span>
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

            <!-- Mobile View (Card List) -->
            <div class="d-md-none p-3 bg-light">
                @forelse($books as $book)
                <div class="card mb-3 border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-3">
                        <div class="d-flex gap-3">
                            <img src="{{ $book->cover_image }}" alt="Cover" width="70" height="100" class="rounded shadow-sm object-fit-cover flex-shrink-0">
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $book->title }}</h6>
                                <p class="text-muted small mb-2 text-truncate">{{ $book->author }}</p>
                                
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $book->category }}</span>
                                    <span class="badge {{ $book->status == 'Available' ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.7rem;">
                                        {{ $book->status }}
                                    </span>
                                </div>
                                <div class="small fw-bold text-dark">
                                    <i class="fas fa-cubes me-1 text-muted"></i> Stok: {{ $book->stock }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-2">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.books.edit', $book->id) }}" class="btn btn-warning btn-sm flex-fill">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="d-inline flex-fill" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-book-open fa-3x mb-3 opacity-25"></i>
                    <p>Belum ada data buku.</p>
                </div>
                @endforelse
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
