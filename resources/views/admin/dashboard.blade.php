<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - UNILAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">UNILAM Admin</a>
            <form action="{{ route('admin.logout') }}" method="POST" class="d-flex">
                @csrf
                <button class="btn btn-outline-danger btn-sm" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Daftar Pengguna ({{ $users->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>No. HP</th>
                                        <th>Bergabung</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($user->profile_photo)
                                                    <img src="{{ $user->profile_photo }}" class="rounded-circle me-2" width="30" height="30" style="object-fit:cover">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px; font-size: 12px;">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge {{ $user->role == 'admin' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td>{{ $user->phone_number }}</td>
                                        <td>{{ $user->created_at->format('d M Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Daftar Buku (Coming Soon)</h5>
                    </div>
                    <div class="card-body">
                         <p class="text-muted">Fitur manajemen buku akan ditampilkan di sini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>