@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Overview</h2>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-custom bg-primary text-white p-3">
                <div class="card-body">
                    <h5 class="card-title">Total Buku</h5>
                    <p class="card-text display-4 fw-bold">{{ $totalBooks }}</p>
                    <i class="fas fa-book fa-3x position-absolute end-0 bottom-0 m-3 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-success text-white p-3">
                <div class="card-body">
                    <h5 class="card-title">Total Pengguna</h5>
                    <p class="card-text display-4 fw-bold">{{ $totalUsers }}</p>
                    <i class="fas fa-users fa-3x position-absolute end-0 bottom-0 m-3 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-warning text-dark p-3">
                <div class="card-body">
                    <h5 class="card-title">Peminjaman Aktif</h5>
                    <p class="card-text display-4 fw-bold">{{ $activeBorrows }}</p>
                    <i class="fas fa-exchange-alt fa-3x position-absolute end-0 bottom-0 m-3 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
