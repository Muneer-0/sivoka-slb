@extends('layouts.dashboard')

@section('title', 'Monitoring Data SLB')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-monitoring.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h4>
        <i class="bi bi-clipboard-data me-2"></i>
        Monitoring Data SLB
    </h4>
    <p>Pantau kelengkapan data program vokasi di seluruh SLB Sumatera Utara</p>
    
    <div class="header-stats">
        <div class="header-stat">
            <i class="bi bi-grid"></i>
            Total: {{ $totalSchools }} SLB
        </div>
        <div class="header-stat">
            <i class="bi bi-building"></i>
            {{ $cities->count() }} Kota/Kab
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card total">
        <i class="bi bi-building stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Total SLB</div>
            <div class="stat-value">{{ $totalSchools }}</div>
        </div>
    </div>
    
    <div class="stat-card sudah">
        <i class="bi bi-check-circle stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Sudah Input Data</div>
            <div class="stat-value">{{ $schoolsWithData }}</div>
            <div class="stat-footer">
                {{ $totalSchools > 0 ? round(($schoolsWithData/$totalSchools)*100) : 0 }}% dari total
            </div>
        </div>
    </div>
    
    <div class="stat-card belum">
        <i class="bi bi-exclamation-triangle stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Belum Input Data</div>
            <div class="stat-value">{{ $schoolsWithoutData }}</div>
            <div class="stat-footer">
                {{ $totalSchools > 0 ? round(($schoolsWithoutData/$totalSchools)*100) : 0 }}% dari total
            </div>
        </div>
    </div>
    
    <div class="stat-card program">
        <i class="bi bi-bar-chart stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Total Program</div>
            <div class="stat-value">{{ $totalPrograms }}</div>
            <div class="stat-footer">
                Rata-rata {{ $totalSchools > 0 ? round($totalPrograms/$totalSchools, 1) : 0 }} program/SLB
            </div>
        </div>
    </div>
</div>

<!-- Progress Card -->
@php $percentage = $totalSchools > 0 ? round(($schoolsWithData/$totalSchools)*100) : 0; @endphp
<div class="progress-card">
    <div class="progress-header">
        <h5>
            <i class="bi bi-pie-chart"></i>
            Progress Pengisian Data
        </h5>
        <span class="progress-percentage">{{ $percentage }}%</span>
    </div>
    
    <div class="progress-bar-container">
        <div class="progress-bar-fill" style="width: {{ $percentage }}%;"></div>
    </div>
    
    <div class="progress-stats">
        <span><i class="bi bi-check-circle-fill text-success"></i> {{ $schoolsWithData }} SLB sudah input</span>
        <span><i class="bi bi-exclamation-triangle-fill text-warning"></i> {{ $schoolsWithoutData }} SLB belum input</span>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-card">
    <div class="filter-title">
        <i class="bi bi-funnel"></i>
        Filter Data Monitoring
    </div>
    
    <form method="GET" action="{{ route('monitoring.index') }}">
        <div class="filter-grid">
            <div class="filter-group">
                <label for="search">
                    <i class="bi bi-search"></i> Cari
                </label>
                <input type="text" name="search" id="search" 
                       placeholder="Nama SLB / NPSN..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="filter-group">
                <label for="city">
                    <i class="bi bi-geo-alt"></i> Kota/Kab
                </label>
                <select name="city" id="city">
                    <option value="">Semua Kota</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" 
                                {{ request('city') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="status">
                    <i class="bi bi-tag"></i> Status Sekolah
                </label>
                <select name="status" id="status">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" 
                                {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="program_status">
                    <i class="bi bi-check-circle"></i> Status Data
                </label>
                <select name="program_status" id="program_status">
                    <option value="">Semua</option>
                    <option value="lengkap" {{ request('program_status') == 'lengkap' ? 'selected' : '' }}>Sudah Input</option>
                    <option value="belum" {{ request('program_status') == 'belum' ? 'selected' : '' }}>Belum Input</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('monitoring.index') }}" class="btn-reset">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabel untuk Desktop -->
<div class="table-container d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>NPSN</th>
                    <th>Nama SLB</th>
                    <th>Kota/Kab</th>
                    <th>Kecamatan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Akreditasi</th>
                    <th class="text-center">Program</th>
                    <th class="text-center">Siswa</th>
                    <th class="text-center">Status Data</th>
                    <th class="text-center" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $index => $school)
                <tr>
                    <td class="text-center">{{ $schools->firstItem() + $index }}</td>
                    <td><span class="npsn-text">{{ $school->npsn }}</span></td>
                    <td>
                        <div class="school-name-cell">{{ $school->name }}</div>
                        @if($school->headmaster)
                            <small class="headmaster-small">
                                <i class="bi bi-person"></i> {{ $school->headmaster }}
                            </small>
                        @endif
                    </td>
                    <td>{{ $school->city }}</td>
                    <td>{{ $school->district }}</td>
                    <td class="text-center">
                        @if($school->status == 'negeri')
                            <span class="badge-negeri">Negeri</span>
                        @else
                            <span class="badge-swasta">Swasta</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($school->accreditation)
                            <span class="badge-akreditasi">{{ $school->accreditation }}</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="program-badge">
                            <i class="bi bi-bar-chart"></i> {{ $school->programs_count }}
                        </span>
                    </td>
                    <td class="text-center">{{ number_format($school->programs_sum_student_count ?? 0) }}</td>
                    <td class="text-center">
                        @if($school->programs_count > 0)
                            <span class="status-badge status-lengkap">
                                <i class="bi bi-check-circle"></i> Lengkap
                            </span>
                        @else
                            <span class="status-badge status-belum">
                                <i class="bi bi-exclamation-triangle"></i> Belum
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('schools.show', $school) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>Belum Ada Data SLB</h5>
                            <p>Tidak ada data SLB yang ditemukan dengan filter yang dipilih.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Cards untuk Mobile -->
<div class="school-cards d-md-none">
    @forelse($schools as $school)
    <div class="school-card">
        <div class="school-header">
            <div>
                <div class="school-title">{{ $school->name }}</div>
                <div class="school-npsn">
                    <i class="bi bi-upc-scan"></i> {{ $school->npsn }}
                </div>
            </div>
            <div>
                @if($school->status == 'negeri')
                    <span class="badge-negeri">Negeri</span>
                @else
                    <span class="badge-swasta">Swasta</span>
                @endif
            </div>
        </div>
        
        @if($school->headmaster)
        <div class="school-headmaster">
            <i class="bi bi-person"></i>
            {{ $school->headmaster }}
        </div>
        @endif
        
        <div class="school-details">
            <div class="detail-item">
                <div class="detail-label">Kota/Kab</div>
                <div class="detail-value">
                    <i class="bi bi-geo-alt"></i> {{ $school->city }}
                </div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Kecamatan</div>
                <div class="detail-value">
                    <i class="bi bi-pin-map"></i> {{ $school->district }}
                </div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Akreditasi</div>
                <div class="detail-value">
                    @if($school->accreditation)
                        <span class="badge-akreditasi">{{ $school->accreditation }}</span>
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
        
        <div class="school-stats">
            <div class="stat-box">
                <div class="label">Program</div>
                <div class="value">{{ $school->programs_count }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Siswa</div>
                <div class="value">{{ number_format($school->programs_sum_student_count ?? 0) }}</div>
            </div>
        </div>
        
        <div class="school-footer">
            <div>
                @if($school->programs_count > 0)
                    <span class="status-badge status-lengkap">
                        <i class="bi bi-check-circle"></i> Lengkap
                    </span>
                @else
                    <span class="status-badge status-belum">
                        <i class="bi bi-exclamation-triangle"></i> Belum
                    </span>
                @endif
            </div>
            <div class="school-actions">
                <a href="{{ route('schools.show', $school) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> Detail
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>Belum Ada Data SLB</h5>
        <p>Tidak ada data SLB yang ditemukan dengan filter yang dipilih.</p>
    </div>
    @endforelse
</div>

<!-- Pagination Responsive -->
@if($schools->hasPages())
<div class="pagination-wrapper">
    <div class="pagination-info">
        Menampilkan {{ $schools->firstItem() }} - {{ $schools->lastItem() }} 
        dari {{ $schools->total() }} data SLB
    </div>
    
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if($schools->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-left"></i>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $schools->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach($schools->getUrlRange(1, $schools->lastPage()) as $page => $url)
            @if($page == $schools->currentPage())
                <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if($schools->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $schools->nextPageUrl() }}" rel="next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-right"></i>
                </span>
            </li>
        @endif
    </ul>
</div>
@endif
@endsection