@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Overview</h2>

    <div class="row g-4">
        <!-- Card Total Books -->
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-primary text-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total Buku</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalBooks }}</h2>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.books.index') }}" class="text-white text-decoration-none small">
                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Total Users -->
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-success text-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total User</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalUsers }}</h2>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.users.index') }}" class="text-white text-decoration-none small">
                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Active Borrows -->
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-warning text-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Peminjaman Aktif</h6>
                        <h2 class="mb-0 fw-bold">{{ $activeBorrows }}</h2>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <!-- Note: Route for borrowings index might not exist yet, so we use '#' or create it later -->
                    <a href="#" class="text-white text-decoration-none small">
                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card card-custom">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Selamat Datang, Admin!</h5>
                </div>
                <div class="card-body">
                    <p>
                        Gunakan menu di sebelah kiri untuk mengelola data buku dan pengguna aplikasi perpustakaan.
                        Pastikan untuk selalu memperbarui stok buku dan memantau status peminjaman.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection