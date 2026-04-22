@extends('layouts.dashboard')

@section('title', 'Dashboard Operator')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-operator-dashboard.css') }}">
@endpush

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <h4 class="welcome-title">Halo, {{ auth()->user()->name }}! 👋</h4>
    <p class="welcome-subtitle">Selamat datang di dashboard operator</p>
    <div class="school-badge">
        <i class="bi bi-building"></i>
        {{ $school->name }}
    </div>
</div>

<!-- TOMBOL EXPORT -->
<div class="d-flex justify-content-end gap-2 mb-3">
    <button onclick="exportSchoolToExcel()" class="btn btn-success">
        <i class="bi bi-file-excel"></i> Export Data Sekolah
    </button>
    <button onclick="exportSchoolToPDF()" class="btn btn-danger">
        <i class="bi bi-file-pdf"></i> Export Data Sekolah
    </button>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon program">
            <i class="bi bi-bar-chart"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Program Vokasi</div>
            <div class="stat-value">{{ $totalPrograms }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon student">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalStudents }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon category">
            <i class="bi bi-tags"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Kategori</div>
            <div class="stat-value">{{ $statsPerCategory->count() }}</div>
        </div>
    </div>
</div>

<!-- Informasi Sekolah -->
<div class="school-info-card">
    <div class="section-header">
        <h5>
            <i class="bi bi-building"></i>
            Informasi Sekolah
        </h5>
        <a href="{{ route('my-school') }}">
            Detail <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    
    <div class="school-info-grid">
        <div class="info-item">
            <span class="label">NPSN</span>
            <span class="value">
                <i class="bi bi-upc-scan"></i>
                {{ $school->npsn }}
            </span>
        </div>
        
        <div class="info-item">
            <span class="label">Nama Sekolah</span>
            <span class="value">
                <i class="bi bi-building"></i>
                {{ $school->name }}
            </span>
        </div>
        
        <div class="info-item">
            <span class="label">Alamat</span>
            <span class="value">
                <i class="bi bi-geo-alt"></i>
                {{ $school->address }}, {{ $school->city }}
            </span>
        </div>
        
        <div class="info-item">
            <span class="label">Kepala Sekolah</span>
            <span class="value">
                <i class="bi bi-person"></i>
                {{ $school->headmaster ?? '-' }}
            </span>
        </div>
        
        <div class="info-item">
            <span class="label">Status</span>
            <span class="value">
                @if($school->status == 'negeri')
                    <span class="badge bg-success">Negeri</span>
                @else
                    <span class="badge bg-warning">Swasta</span>
                @endif
            </span>
        </div>
        
        <div class="info-item">
            <span class="label">Akreditasi</span>
            <span class="value">
                @if($school->accreditation)
                    <span class="badge bg-info">{{ $school->accreditation }}</span>
                @else
                    -
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Program Vokasi Terbaru -->
<div class="school-info-card">
    <div class="section-header">
        <h5>
            <i class="bi bi-clock-history"></i>
            Program Vokasi Terbaru
        </h5>
        <a href="{{ route('my-programs') }}">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    
    <div class="program-cards">
        @forelse($programs->take(3) as $program)
        <div class="program-item">
            <div class="program-header">
                <div>
                    <div class="program-name">{{ $program->program_name }}</div>
                    <div class="program-category">
                        <i class="bi bi-tag"></i>
                        {{ $program->category->name }}
                    </div>
                </div>
                <div>
                    @if($program->status == 'active')
                        <span class="program-status status-active">Aktif</span>
                    @else
                        <span class="program-status status-inactive">Nonaktif</span>
                    @endif
                </div>
            </div>
            
            @if($program->achievements)
            <div class="program-achievement">
                <i class="bi bi-trophy"></i>
                {{ Str::limit($program->achievements, 50) }}
            </div>
            @endif
            
            <div class="program-stats">
                <div class="program-stat">
                    <div class="label">Siswa</div>
                    <div class="value">{{ $program->student_count }}</div>
                </div>
                <div class="program-stat">
                    <div class="label">Guru</div>
                    <div class="value">{{ $program->teacher_count ?? 0 }}</div>
                </div>
                <div class="program-stat">
                    <div class="label">Fasilitas</div>
                    <div class="value">{{ $program->facilities ? '✓' : '-' }}</div>
                </div>
            </div>
            
            <div class="program-actions">
                <a href="{{ route('programs.show', $program) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                    <i class="bi bi-eye"></i> Detail
                </a>
                <a href="{{ route('programs.edit', $program) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('programs.destroy', $program) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p>Belum ada program vokasi</p>
            <a href="{{ route('programs.create') }}" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Program
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Tombol Detail Sekolah (Mobile) -->
<div class="d-md-none mt-3">
    <a href="{{ route('my-school') }}" class="btn-detail">
        <i class="bi bi-building"></i>
        Lihat Detail Sekolah
        <i class="bi bi-arrow-right"></i>
    </a>
</div>

<!-- Tombol Tambah Program (Mobile) -->
@if($programs->count() == 0)
<div class="d-md-none mt-2">
    <a href="{{ route('programs.create') }}" class="btn-detail" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <i class="bi bi-plus-circle"></i>
        Tambah Program Pertama
    </a>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Optional: Add any JavaScript for interactivity
    console.log('Operator dashboard loaded');
</script>
<script>
    function exportSchoolToExcel() {
        window.location.href = '{{ route("operator.export.school.excel") }}';
    }
    
    function exportSchoolToPDF() {
        window.location.href = '{{ route("operator.export.school.pdf") }}';
    }
</script>
@endpush