@extends('layouts.dashboard')

@section('title', 'Program Vokasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-programs.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h4>
        <i class="bi bi-bar-chart me-2"></i>
        Program Vokasi
    </h4>
    <p>Kelola program vokasi di seluruh SLB Sumatera Utara</p>
    
    <div class="header-stats">
        <div class="header-stat">
            <i class="bi bi-grid"></i>
            Total: {{ $programs->total() }} Program
        </div>
        <div class="header-stat">
            <i class="bi bi-building"></i>
            {{ $cities->count() }} Kota/Kab
        </div>
        <div class="header-stat">
            <i class="bi bi-tags"></i>
            {{ $categories->count() }} Kategori
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="bi bi-bar-chart"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Program</div>
            <div class="stat-value">{{ $programs->total() }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon schools">
            <i class="bi bi-building"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">SLB Aktif</div>
            <div class="stat-value">{{ $programs->groupBy('school_id')->count() }}</div>
            <div class="stat-desc">sekolah dengan program</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon students">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $programs->sum('student_count') }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon categories">
            <i class="bi bi-tags"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Kategori</div>
            <div class="stat-value">{{ $categories->count() }}</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-card">
    <div class="filter-title">
        <i class="bi bi-funnel"></i>
        Filter Program
    </div>
    
    <form method="GET" action="{{ route('programs.index') }}">
        <div class="filter-grid">
            <div class="filter-group">
                <label for="search">
                    <i class="bi bi-search"></i> Cari Program
                </label>
                <input type="text" name="search" id="search" 
                       placeholder="Nama program..." 
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
                <label for="category">
                    <i class="bi bi-tags"></i> Kategori
                </label>
                <select name="category" id="category">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('programs.index') }}" class="btn-reset">
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
                    <th>Program</th>
                    <th>SLB</th>
                    <th>Kota</th>
                    <th>Kategori</th>
                    <th class="text-center">Siswa</th>
                    <th class="text-center">Guru</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $index => $program)
                <tr>
                    <td class="text-center">{{ $programs->firstItem() + $index }}</td>
                    <td>
                        <div class="program-name-cell">{{ $program->program_name }}</div>
                        @if($program->achievements)
                            <span class="achievement-small">
                                <i class="bi bi-trophy"></i> {{ Str::limit($program->achievements, 30) }}
                            </span>
                        @endif
                    </td>
                    <td>{{ $program->school->name }}</td>
                    <td>{{ $program->school->city }}</td>
                    <td>{{ $program->category->name }}</td>
                    <td class="text-center">{{ $program->student_count }}</td>
                    <td class="text-center">{{ $program->teacher_count ?? 0 }}</td>
                    <td class="text-center">
                        @if($program->status == 'active')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('programs.show', $program) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && auth()->user()->school_id == $program->school_id))
                                <a href="{{ route('programs.edit', $program) }}" 
                                   class="btn btn-sm btn-outline-warning" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('programs.destroy', $program) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus program ini?')">
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
                            <h5>Belum Ada Program Vokasi</h5>
                            <p>Belum ada program vokasi yang terdaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Cards untuk Mobile -->
<div class="program-cards d-md-none">
    @forelse($programs as $program)
    <div class="program-card">
        <div class="program-header">
            <div>
                <div class="program-title">{{ $program->program_name }}</div>
                <div class="program-meta">
                    <span class="meta-item">
                        <i class="bi bi-building"></i> {{ $program->school->name }}
                    </span>
                    <span class="meta-item">
                        <i class="bi bi-geo-alt"></i> {{ $program->school->city }}
                    </span>
                    <span class="meta-item">
                        <i class="bi bi-tag"></i> {{ $program->category->name }}
                    </span>
                </div>
            </div>
            <div>
                @if($program->status == 'active')
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">Nonaktif</span>
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
            <a href="{{ route('programs.show', $program) }}" 
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye"></i> Detail
            </a>
            
            @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && auth()->user()->school_id == $program->school_id))
                <a href="{{ route('programs.edit', $program) }}" 
                   class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('programs.destroy', $program) }}" 
                      method="POST" 
                      class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Yakin ingin menghapus program ini?')">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>Belum Ada Program Vokasi</h5>
        <p>Belum ada program vokasi yang terdaftar.</p>
    </div>
    @endforelse
</div>

<!-- Pagination Responsive -->
@if($programs->hasPages())
<div class="pagination-wrapper">
    <div class="pagination-info">
        Menampilkan {{ $programs->firstItem() }} - {{ $programs->lastItem() }} 
        dari {{ $programs->total() }} program
    </div>
    
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if($programs->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-left"></i>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $programs->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach($programs->getUrlRange(1, $programs->lastPage()) as $page => $url)
            @if($page == $programs->currentPage())
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
        @if($programs->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $programs->nextPageUrl() }}" rel="next">
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