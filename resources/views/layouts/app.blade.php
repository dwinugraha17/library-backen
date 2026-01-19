<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNILAM Library Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #212529;
            --sidebar-hover: #2c3034;
            --primary-color: #0d6efd;
        }
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        
        /* Sidebar Styling */
        .sidebar { background-color: var(--sidebar-bg); color: #c2c7d0; transition: all 0.3s; }
        .sidebar .nav-link { color: #c2c7d0; padding: 12px 15px; border-radius: 4px; margin-bottom: 2px; }
        .sidebar .nav-link:hover { background-color: var(--sidebar-hover); color: white; }
        .sidebar .nav-link.active { background-color: var(--primary-color); color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        .sidebar .nav-link i { width: 25px; text-align: center; margin-right: 8px; }
        .sidebar-brand { padding: 1.2rem 1rem; border-bottom: 1px solid #4b545c; margin-bottom: 1rem; }
        
        /* Content Styling */
        .content-wrapper { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 20px; }
        .card-custom { border: none; border-radius: 0.5rem; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); background: white; }

        /* Responsive Layout */
        @media (min-width: 992px) {
            .sidebar { 
                width: var(--sidebar-width); 
                height: 100vh; 
                position: fixed; 
                top: 0; 
                left: 0; 
                z-index: 1000; 
                overflow-y: auto;
                /* Force visibility on desktop to override offcanvas behavior */
                display: flex !important;
                flex-direction: column;
                transform: none !important;
                visibility: visible !important;
            }
            .content-wrapper { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); }
            .mobile-header { display: none; }
        }
    </style>
</head>
<body>

<!-- Mobile Header (Sticky) -->
<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm mobile-header d-lg-none">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1 fw-bold"><i class="fas fa-book-reader me-2"></i>UNILAM</span>
        <button class="btn btn-dark border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars fa-lg"></i>
        </button>
    </div>
</nav>

<div class="content-wrapper">
    <!-- Sidebar (Offcanvas on mobile, Fixed on Desktop) -->
    <div class="offcanvas-lg offcanvas-start sidebar bg-dark" tabindex="-1" id="sidebarMenu">
        <div class="offcanvas-header bg-dark text-white border-bottom border-secondary d-lg-none">
            <h5 class="offcanvas-title fw-bold"><i class="fas fa-book-reader me-2"></i>UNILAM Admin</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"></button>
        </div>

        <div class="offcanvas-body p-0 d-flex flex-column h-100">
            <!-- Brand Logo (Desktop) -->
            <a href="/" class="sidebar-brand text-white text-decoration-none d-none d-lg-block">
                <span class="fs-4 fw-bold"><i class="fas fa-book-reader me-2"></i>UNILAM Admin</span>
            </a>

            <!-- Menu Items -->
            <div class="p-3 flex-grow-1">
                <p class="text-uppercase text-muted small fw-bold px-2 mb-2">Main Menu</p>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.books.index') }}" class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i> Manajemen Buku
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Manajemen User
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Footer Sidebar -->
            <div class="p-3 border-top border-secondary mt-auto">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content bg-light">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
