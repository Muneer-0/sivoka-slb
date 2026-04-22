@extends('layouts.dashboard')

@section('title', 'Detail Program Vokasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-program-detail.css') }}">
@endpush

@section('content')
<!-- Header -->
<div class="page-header">
    <h4>
        <i class="bi bi-bar-chart me-2"></i>
        {{ $program->program_name }}
    </h4>
    <p>Informasi lengkap program vokasi</p>
    <div class="program-badge">
        <i class="bi bi-tag"></i>
        {{ $program->category->name }}
    </div>
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && auth()->user()->school_id == $program->school_id))
        <a href="{{ route('programs.edit', $program) }}" class="btn-edit">
            <i class="bi bi-pencil"></i> Edit Program
        </a>
    @endif
    
    @if(auth()->user()->isOperator())
        <a href="{{ route('my-programs') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Program Saya
        </a>
    @else
        <a href="{{ route('programs.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Program
        </a>
    @endif
</div>

<!-- Stats Row -->
<div class="stats-row">
    <!-- Card Total Siswa -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ number_format($program->student_count) }}</div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>
    
    <!-- Card Total Guru -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-person-badge"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ number_format($program->teacher_count ?? 0) }}</div>
            <div class="stat-label">Total Guru</div>
        </div>
    </div>
    
    <!-- Card Nama Sekolah (Bisa ke bawah, tanpa limit) -->
    <div class="stat-card stat-card-school">
        <div class="stat-content">
            <div class="stat-school-name" title="{{ $program->school->name }}">
                {{ $program->school->name }}
            </div>
            <!-- TIDAK ADA LABEL -->
        </div>
    </div>
</div>

<!-- Info Grid (2 kolom) -->
<div class="info-grid">
    <div class="info-card">
        <div class="card-header">
            <i class="bi bi-info-circle"></i>
            <h5>Informasi Program</h5>
        </div>
        <div class="info-row">
            <span class="info-label">Nama Program</span>
            <span class="info-value">{{ $program->program_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kategori</span>
            <span class="info-value">{{ $program->category->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value">
                @if($program->status == 'active')
                    <span class="status-badge status-active">Aktif</span>
                @else
                    <span class="status-badge status-inactive">Nonaktif</span>
                @endif
            </span>
        </div>
    </div>
    
    <div class="info-card">
        <div class="card-header">
            <i class="bi bi-building"></i>
            <h5>Informasi Sekolah</h5>
        </div>
        <div class="info-row">
            <span class="info-label">SLB</span>
            <span class="info-value">{{ $program->school->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kota/Kab</span>
            <span class="info-value">{{ $program->school->city }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kecamatan</span>
            <span class="info-value">{{ $program->school->district }}</span>
        </div>
    </div>
</div>

<!-- Tabel Gabungan: Fasilitas, Deskripsi, Produk -->
<div class="detail-table-card">
    <table class="detail-table">
        @if($program->facilities)
        <tr>
            <td class="icon-cell">
                <i class="bi bi-tools"></i> Fasilitas
            </td>
            <td>{{ $program->facilities }}</td>
        </tr>
        @endif
        
        @if($program->description)
        <tr>
            <td class="icon-cell">
                <i class="bi bi-journal-text"></i> Deskripsi
            </td>
            <td>{{ $program->description }}</td>
        </tr>
        @endif
        
        @if($program->products)
        <tr>
            <td class="icon-cell">
                <i class="bi bi-box-seam"></i> Produk Unggulan
            </td>
            <td>{{ $program->products }}</td>
        </tr>
        @endif
        
        @if(!$program->facilities && !$program->description && !$program->products)
        <tr>
            <td colspan="2" style="text-align: center; color: #6c757d;">
                <i class="bi bi-info-circle"></i> Belum ada informasi tambahan
            </td>
        </tr>
        @endif
    </table>
</div>

<!-- Prestasi -->
@if($program->achievements)
<div class="achievement-card">
    <div class="achievement-header">
        <i class="bi bi-trophy"></i>
        <h5>Prestasi</h5>
    </div>
    <div class="achievement-text">
        {{ $program->achievements }}
    </div>
</div>
@endif

<!-- Metadata -->
<div class="metadata-card">
    <div class="metadata-list">
        <span><i class="bi bi-person"></i> Dibuat: {{ $program->creator->name ?? 'Unknown' }}</span>
        <span><i class="bi bi-calendar"></i> Tanggal: {{ $program->created_at->format('d/m/Y H:i') }}</span>
        @if($program->updated_by)
        <span><i class="bi bi-pencil"></i> Diupdate: {{ $program->updated_at->format('d/m/Y H:i') }}</span>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-program-detail.js') }}"></script>
@endpush