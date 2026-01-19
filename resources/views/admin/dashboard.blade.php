@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold text-gray-800">Dashboard Overview</h2>
    </div>

    <div class="row g-4 mb-4">
        <!-- Card Total Books -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-3 bg-primary text-white h-100 shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small fw-bold text-white-50">Total Buku</h6>
                        <h2 class="mb-0 fw-bold display-6">{{ $totalBooks }}</h2>
                    </div>
                    <div class="fs-1 opacity-25">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="mt-3 border-top border-white border-opacity-25 pt-2">
                    <a href="{{ route('admin.books.index') }}" class="text-white text-decoration-none small d-flex align-items-center">
                        Lihat Detail <i class="fas fa-arrow-right ms-auto"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Total Users -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-3 bg-success text-white h-100 shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small fw-bold text-white-50">Total User</h6>
                        <h2 class="mb-0 fw-bold display-6">{{ $totalUsers }}</h2>
                    </div>
                    <div class="fs-1 opacity-25">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3 border-top border-white border-opacity-25 pt-2">
                    <a href="{{ route('admin.users.index') }}" class="text-white text-decoration-none small d-flex align-items-center">
                        Lihat Detail <i class="fas fa-arrow-right ms-auto"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Active Borrows -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-3 bg-warning text-white h-100 shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small fw-bold text-white-50">Peminjaman Aktif</h6>
                        <h2 class="mb-0 fw-bold display-6">{{ $activeBorrows }}</h2>
                    </div>
                    <div class="fs-1 opacity-25">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="mt-3 border-top border-white border-opacity-25 pt-2">
                    <a href="#" class="text-white text-decoration-none small d-flex align-items-center">
                        Lihat Detail <i class="fas fa-arrow-right ms-auto"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Informasi Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info mb-0">
                        <h6 class="alert-heading fw-bold">Selamat Datang, Admin!</h6>
                        <p class="mb-0">
                            Gunakan menu di sebelah kiri untuk mengelola data buku dan pengguna aplikasi perpustakaan.
                            Pastikan untuk selalu memperbarui stok buku dan memantau status peminjaman.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection