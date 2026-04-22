@extends('layouts.dashboard')

@section('title', 'Manajemen User')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-users.css') }}">
<link rel="stylesheet" href="{{ asset('css/sivoka-user-filter.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #2c3aff 100%); border-radius: 16px; padding: 20px; margin-bottom: 20px; color: white;">
        <h4 class="mb-2">
            <i class="bi bi-people me-2"></i>
            Manajemen User
        </h4>
        <p class="mb-0 opacity-75">Kelola akun pengguna sistem SiVOKA-SLB</p>
    </div>

    <!-- ===== FILTER SECTION ===== -->
    <div class="filter-section">
        <div class="filter-title">
            <i class="bi bi-funnel"></i>
            Filter Data User
        </div>
        
        <form method="GET" action="{{ route('users.index') }}" id="userFilterForm">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="search">
                        <i class="bi bi-search"></i> Cari
                    </label>
                    <input type="text" name="search" id="search" 
                           placeholder="Nama / Email / NPSN..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="filter-group">
                    <label for="role">
                        <i class="bi bi-person-badge"></i> Role
                    </label>
                    <select name="role" id="role">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pimpinan" {{ request('role') == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                        <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="school_id">
                        <i class="bi bi-building"></i> SLB
                    </label>
                    <select name="school_id" id="school_id">
                        <option value="">Semua SLB</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                {{ Str::limit($school->name, 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="bi bi-search"></i> Terapkan Filter
                    </button>
                    <a href="#" id="resetFilters" class="btn-reset">
                        <i class="bi bi-arrow-repeat"></i> Reset Filter
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <div class="btn-group-left">
            <a href="{{ route('users.create') }}" class="btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah User
            </a>
            <button type="button" class="btn-excel" data-bs-toggle="modal" data-bs-target="#importUserModal">
                <i class="bi bi-file-earmark-excel"></i> Import User
            </button>
        </div>
    </div>

    <!-- Alert setelah reset password -->
    @if(session('reset_password'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-key-fill fs-3 me-3 text-warning"></i>
            <div>
                <strong>Password untuk {{ session('reset_password.user_name') }} telah direset!</strong><br>
                Password baru: 
                <code class="password-cell fs-5 fw-bold">{{ session('reset_password.password') }}</code>
                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ session('reset_password.password') }}')">
                    <i class="bi bi-copy"></i> Copy
                </button>
                <div class="small text-muted mt-1">
                    <i class="bi bi-info-circle"></i> 
                    Password akan tetap terlihat di tabel hingga user login.
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            @if(session('errors_detail'))
                <ul class="mt-2 mb-0">
                    @foreach(session('errors_detail') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <!-- Tabel User -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama</th>
                        <th>Email / NPSN</th>
                        <th style="width: 100px;">Role</th>
                        <th style="width: 130px;">Password</th>
                        <th class="school-cell">SLB</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td class="text-center">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="user-name">{{ Str::limit($user->name, 30) }}</span>
                            </div>
                        </td>
                        <td>{{ Str::limit($user->email ?? $user->npsn, 25) }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge-admin">Admin</span>
                            @elseif($user->role == 'pimpinan')
                                <span class="badge-pimpinan">Pimpinan</span>
                            @else
                                <span class="badge-operator">Operator</span>
                            @endif
                        </td>
                        
                        <!-- KOLOM PASSWORD -->
                        <td>
                            @if($user->show_password && $user->temp_password)
                                <code class="password-cell">{{ $user->temp_password }}</code>
                                <span class="badge-reset">
                                    <i class="bi bi-clock-history"></i> Belum Login
                                </span>
                            @else
                                <span class="text-muted">••••••••</span>
                            @endif
                        </td>
                        
                        <td class="school-cell">
                            @if($user->school)
                                <span class="school-name-display" title="{{ $user->school->name }}">
                                    {{ Str::limit($user->school->name, 35) }}
                                </span>
                                <span class="school-npsn-display">
                                    <i class="bi bi-upc-scan"></i> {{ $user->school->npsn }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        
                        <!-- KOLOM AKSI -->
                        <td>
                            <div class="action-icons">
                                <a href="{{ route('users.edit', $user) }}" class="btn-icon btn-icon-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <!-- Tombol Reset Password -->
                                <form action="{{ route('users.reset-password', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Reset password untuk {{ $user->name }}? Password baru akan ditampilkan setelah reset.')">
                                    @csrf
                                    <button type="submit" class="btn-icon btn-icon-info" title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </form>
                                
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h5>Belum Ada Data User</h5>
                                <p>Silakan tambah user baru atau import dari Excel.</p>
                                <a href="{{ route('users.create') }}" class="btn-primary" style="display: inline-flex;">
                                    <i class="bi bi-plus-circle"></i> Tambah User
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Responsive -->
        @if($users->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} 
                dari {{ $users->total() }} data user
            </div>
            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Import User -->
<div class="modal fade" id="importUserModal" tabindex="-1" aria-labelledby="importUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importUserModalLabel">
                    <i class="bi bi-file-earmark-excel me-2"></i>
                    Import User dari Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Petunjuk:</strong>
                        <ul class="mb-0 mt-2">
                            <li>File: <strong>.xlsx, .xls, .csv</strong> (max 5MB)</li>
                            <li>Kolom wajib: <strong>name, email, role</strong></li>
                            <li>Role: <strong>admin, pimpinan, operator</strong></li>
                            <li>Operator wajib isi <strong>npsn</strong></li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file" class="form-label fw-bold">
                            <i class="bi bi-file-earmark-excel me-1"></i> Pilih File
                        </label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('users.import.template') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-download me-1"></i> Download Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Password berhasil disalin!');
    }
    
    // Reset filter
    document.getElementById('resetFilters')?.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = window.location.pathname;
    });
</script>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-user-filter.js') }}"></script>
@endpush