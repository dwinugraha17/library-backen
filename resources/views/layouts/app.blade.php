<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNILAM Library Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background-color: #343a40; color: white; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 10px 15px; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: white; }
        .content { padding: 20px; }
        .card-custom { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        /* Desktop Layout */
        @media (min-width: 992px) {
            .sidebar {
                width: 250px;
                min-height: 100vh;
            }
        }
    </style>
</head>
<body>

<!-- Mobile Navbar (Visible only on small screens) -->
<nav class="navbar navbar-dark bg-dark d-lg-none p-3 mb-3">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1"><i class="fas fa-book-reader me-2"></i>UNILAM</span>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="offcanvas-lg offcanvas-start sidebar d-flex flex-column flex-shrink-0 p-3" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <!-- Mobile Header inside Sidebar -->
        <div class="d-lg-none d-flex justify-content-between align-items-center mb-4">
            <span class="fs-4 fw-bold text-white">Menu</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>

        <a href="/" class="d-none d-lg-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4 fw-bold"><i class="fas fa-book-reader me-2"></i>UNILAM</span>
        </a>
        <hr class="d-none d-lg-block">
        
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.books.index') }}" class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                    <i class="fas fa-book me-2"></i> Manajemen Buku
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i> Manajemen User
                </a>
            </li>
        </ul>
        <hr>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 content w-100">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
