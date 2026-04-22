<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - SiVOKA-SLB</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- ===== CUSTOM CSS SIVOKA-SLB ===== -->
    <link rel="stylesheet" href="{{ asset('css/sivoka-main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sivoka-tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sivoka-pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sivoka-filters.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sivoka-stats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sivoka-sidebar-dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sivoka-modal.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (untuk mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="sidebar-close d-md-none" id="sidebarClose">
                <i class="bi bi-x-lg"></i>
            </button>
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain; border-radius: 50%; margin-bottom: 10px;">
            <h3>SiVOKA-SLB</h3>
            <p>Sistem Informasi Vokasi SLB</p>
        </div>

        <div class="sidebar-menu">
            @auth
                @if(auth()->user()->isAdmin())
                    <!-- ===== ADMIN SIDEBAR DENGAN DROPDOWN ===== -->
                    
                    <div class="menu-heading">Dashboard</div>
                    <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="menu-heading">Data Master</div>
                    <div class="has-dropdown">
                        <a href="#" class="menu-item">
                            <i class="bi bi-database"></i>
                            <span>Master Data</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-submenu">
                            <li>
                                <a href="{{ route('schools.index') }}" class="menu-item dropdown-menu-item {{ (request()->routeIs('schools.index') || request()->routeIs('schools.create') || request()->routeIs('schools.edit') || request()->routeIs('schools.show')) && !request()->routeIs('schools.peta') ? 'active' : '' }}">
                                    <i class="bi bi-building"></i> Data SLB
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('schools.import.form') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('schools.import*') ? 'active' : '' }}">
                                    <i class="bi bi-file-earmark-excel"></i> Import Excel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categories.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('categories*') ? 'active' : '' }}">
                                    <i class="bi bi-tags"></i> Kategori Vokasi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('programs.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('programs.index') ? 'active' : '' }}">
                                    <i class="bi bi-bar-chart"></i> Program Vokasi
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="menu-heading">Monitoring & Laporan</div>
                    <div class="has-dropdown">
                        <a href="#" class="menu-item">
                            <i class="bi bi-graph-up"></i>
                            <span>Monitoring</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-submenu">
                            <li>
                                <a href="{{ route('monitoring.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('monitoring*') ? 'active' : '' }}">
                                    <i class="bi bi-clipboard-data"></i> Monitoring Data
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('reports*') ? 'active' : '' }}">
                                    <i class="bi bi-file-text"></i> Laporan
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="menu-heading">Pemetaan</div>
                    <a href="{{ route('schools.peta') }}" class="menu-item {{ request()->routeIs('schools.peta') ? 'active' : '' }}">
                        <i class="bi bi-map"></i>
                        <span>Peta SLB Sumatera Utara</span>
                    </a>

                    <div class="menu-heading">Pengaturan</div>
                    <div class="has-dropdown">
                        <a href="#" class="menu-item">
                            <i class="bi bi-gear"></i>
                            <span>Pengaturan</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-submenu">
                            <li>
                                <a href="{{ route('users.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                                    <i class="bi bi-people"></i> Manajemen User
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="menu-heading">Informasi</div>
                    <a href="{{ route('about') }}" class="menu-item {{ request()->routeIs('about') ? 'active' : '' }}">
                        <i class="bi bi-info-circle"></i>
                        <span>Tentang Sistem</span>
                    </a>

                @elseif(auth()->user()->isPimpinan())
                    <!-- ===== PIMPINAN SIDEBAR DENGAN DROPDOWN ===== -->
                    
                    <div class="menu-heading">Dashboard</div>
                    <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="menu-heading">Data & Laporan</div>
                    <div class="has-dropdown">
                        <a href="#" class="menu-item">
                            <i class="bi bi-bar-chart"></i>
                            <span>Program Vokasi</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-submenu">
                            <li>
                                <a href="{{ route('programs.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('programs.index') ? 'active' : '' }}">
                                    <i class="bi bi-list"></i> Semua Program
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('monitoring.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('monitoring*') ? 'active' : '' }}">
                                    <i class="bi bi-clipboard-data"></i> Monitoring Data
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index') }}" class="menu-item dropdown-menu-item {{ request()->routeIs('reports*') ? 'active' : '' }}">
                                    <i class="bi bi-file-text"></i> Laporan
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="menu-heading">Pemetaan</div>
                    <a href="{{ route('schools.peta') }}" class="menu-item {{ request()->routeIs('schools.peta') ? 'active' : '' }}">
                        <i class="bi bi-map"></i>
                        <span>Peta SLB Sumatera Utara</span>
                    </a>

                    <div class="menu-heading">Informasi</div>
                    <a href="{{ route('about') }}" class="menu-item {{ request()->routeIs('about') ? 'active' : '' }}">
                        <i class="bi bi-info-circle"></i>
                        <span>Tentang Sistem</span>
                    </a>

                @elseif(auth()->user()->isOperator())
                    <!-- ===== OPERATOR SIDEBAR DENGAN DROPDOWN ===== -->
                    
                    @php
                        $isMySchoolActive = request()->routeIs('my-school') || request()->routeIs('schools.show');
                        $isMyProgramsActive = request()->routeIs('my-programs');
                        $isCreateProgramActive = request()->routeIs('programs.create') || request()->routeIs('programs.edit') || request()->routeIs('programs.show');
                        $isSchoolMenuActive = $isMySchoolActive || $isMyProgramsActive || $isCreateProgramActive;
                    @endphp
                    
                    <div class="menu-heading">Dashboard</div>
                    <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="menu-heading">Sekolah Saya</div>
                    <div class="has-dropdown {{ $isSchoolMenuActive ? 'active' : '' }}">
                        <a href="#" class="menu-item">
                            <i class="bi bi-building"></i>
                            <span>Data Sekolah</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </a>
                        <ul class="dropdown-submenu {{ $isSchoolMenuActive ? 'show' : '' }}">
                            <li>
                                <a href="{{ route('my-school') }}" class="menu-item dropdown-menu-item {{ $isMySchoolActive ? 'active' : '' }}">
                                    <i class="bi bi-info-circle"></i> Profil Sekolah
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('my-programs') }}" class="menu-item dropdown-menu-item {{ $isMyProgramsActive ? 'active' : '' }}">
                                    <i class="bi bi-list"></i> Program Vokasi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('programs.create') }}" class="menu-item dropdown-menu-item {{ $isCreateProgramActive ? 'active' : '' }}">
                                    <i class="bi bi-plus-circle"></i> Tambah Program
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="menu-heading">Informasi</div>
                    <a href="{{ route('about') }}" class="menu-item {{ request()->routeIs('about') ? 'active' : '' }}">
                        <i class="bi bi-info-circle"></i>
                        <span>Tentang Sistem</span>
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <i class="bi bi-list menu-toggle fs-3" id="menu-toggle"></i>
                </div>

                <div class="dropdown">
                    <button class="btn user-dropdown-btn dropdown-toggle d-flex align-items-center" 
                            type="button" 
                            id="userDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <div class="user-info me-2">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">
                                @if(auth()->user()->isAdmin()) Administrator
                                @elseif(auth()->user()->isPimpinan()) Pimpinan
                                @else Operator Sekolah
                                @endif
                            </div>
                        </div>
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="bi bi-key me-2"></i> Ganti Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Modal Ganti Password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="bi bi-key me-2"></i> Ganti Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordForm" method="POST" action="{{ route('profile.change-password') }}">
                    @csrf
                    <div class="modal-body">
                        <div id="passwordAlert" class="alert d-none"></div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password lama" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitPasswordBtn">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ===== CUSTOM JAVASCRIPT SIVOKA-SLB ===== -->
    <script src="{{ asset('js/sivoka-core.js') }}"></script>
    <script src="{{ asset('js/sivoka-dropdown.js') }}"></script>
    <script src="{{ asset('js/sivoka-forms.js') }}"></script>
    <script src="{{ asset('js/sivoka-sidebar-dropdown.js') }}"></script>
    <script src="{{ asset('js/sivoka-password-modal.js') }}"></script>

    @stack('scripts')
</body>
</html>