@extends('layouts.dashboard')

@section('title', 'Data SLB')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-schools.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h4>
        <i class="bi bi-building me-2"></i>
        Data SLB
    </h4>
    <p>Kelola data Sekolah Luar Biasa di Sumatera Utara</p>
    
    <div class="header-stats">
        <div class="header-stat">
            <i class="bi bi-grid"></i>
            Total: {{ $schools->total() }} SLB
        </div>
        <div class="header-stat">
            <i class="bi bi-building"></i>
            {{ $cities->count() }} Kota/Kab
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    @php
        $negeri = $schools->where('status', 'negeri')->count();
        $swasta = $schools->where('status', 'swasta')->count();
        $akreditasiA = $schools->where('accreditation', 'A')->count();
    @endphp
    
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="bi bi-building"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total SLB</div>
            <div class="stat-value">{{ $schools->total() }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon negeri">
            <i class="bi bi-building"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Negeri</div>
            <div class="stat-value">{{ $negeri }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon swasta">
            <i class="bi bi-building"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Swasta</div>
            <div class="stat-value">{{ $swasta }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon akreditasi">
            <i class="bi bi-award"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Akreditasi A</div>
            <div class="stat-value">{{ $akreditasiA }}</div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex gap-2">
        <a href="{{ route('schools.import.form') }}" class="btn-add">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </a>
        <a href="{{ route('schools.create') }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Tambah SLB
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-card">
    <div class="filter-title">
        <i class="bi bi-funnel"></i>
        Filter Data SLB
    </div>
    
    <form method="GET" action="{{ route('schools.index') }}">
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
                    <i class="bi bi-tag"></i> Status
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
            
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('schools.index') }}" class="btn-reset">
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
                    <th class="text-center" style="width: 120px;">Aksi</th>
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
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('schools.show', $school) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('schools.edit', $school) }}" 
                                   class="btn btn-sm btn-outline-warning" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('schools.destroy', $school) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>Belum Ada Data SLB</h5>
                            <p>Mulai dengan menambahkan data SLB pertama.</p>
                            <a href="{{ route('schools.create') }}" class="btn-filter">
                                <i class="bi bi-plus-circle"></i> Tambah SLB
                            </a>
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
            
            <div class="detail-item">
                <div class="detail-label">Program</div>
                <div class="detail-value">
                    <span class="program-badge">
                        <i class="bi bi-bar-chart"></i> {{ $school->programs_count }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="school-footer">
            <div></div>
            <div class="school-actions">
                <a href="{{ route('schools.show', $school) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                </a>
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('schools.edit', $school) }}" 
                       class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('schools.destroy', $school) }}" 
                          method="POST" 
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Yakin ingin menghapus data ini?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>Belum Ada Data SLB</h5>
        <p>Mulai dengan menambahkan data SLB pertama.</p>
        <a href="{{ route('schools.create') }}" class="btn-filter">
            <i class="bi bi-plus-circle"></i> Tambah SLB
        </a>
    </div>
    @endforelse
</div>

<!-- ===== PAGINATION RESPONSIVE ===== -->
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