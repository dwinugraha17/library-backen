@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h2 class="h3 fw-bold text-gray-800 mb-0">Manajemen User</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah User
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <!-- Desktop View (Table) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Role</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle me-2 fw-bold" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone_number ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada data user.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View (Card List) -->
            <div class="d-md-none p-3 bg-light">
                @forelse($users as $user)
                <div class="card mb-3 border border-light shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle me-3 fw-bold flex-shrink-0" style="width: 40px; height: 40px; font-size: 1rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-truncate" style="max-width: 150px;">{{ $user->name }}</h6>
                                    <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' }} mt-1" style="font-size: 0.7rem;">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                            <!-- Action Buttons -->
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning py-1 px-2">
                                    <i class="fas fa-edit fa-xs"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger py-1 px-2">
                                        <i class="fas fa-trash fa-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-light rounded p-2 small text-muted">
                            <div class="d-flex mb-1">
                                <i class="fas fa-envelope mt-1 me-2 text-secondary" style="width: 15px;"></i>
                                <span class="text-truncate">{{ $user->email }}</span>
                            </div>
                            <div class="d-flex">
                                <i class="fas fa-phone mt-1 me-2 text-secondary" style="width: 15px;"></i>
                                <span>{{ $user->phone_number ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                    <p>Belum ada data user.</p>
                </div>
                @endforelse
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
