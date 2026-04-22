@extends('layouts.dashboard')

@section('title', 'Kategori Vokasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-categories.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h4>
        <i class="bi bi-tags me-2"></i>
        Kategori Vokasi
    </h4>
    <p>Kelola kategori program vokasi di Sumatera Utara</p>
    
    <div class="header-stats">
        <div class="header-stat">
            <i class="bi bi-grid"></i>
            Total: {{ $categories->total() }} Kategori
        </div>
        <div class="header-stat">
            <i class="bi bi-bar-chart"></i>
            Dengan Program: {{ $categories->where('programs_count', '>', 0)->count() }}
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="bi bi-tags"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Kategori</div>
            <div class="stat-value">{{ $categories->total() }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon with-icon">
            <i class="bi bi-emoji-smile"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Dengan Icon</div>
            <div class="stat-value">{{ $categories->whereNotNull('icon')->count() }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon with-programs">
            <i class="bi bi-bar-chart"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Dengan Program</div>
            <div class="stat-value">{{ $categories->where('programs_count', '>', 0)->count() }}</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon popular">
            <i class="bi bi-star"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Program</div>
            <div class="stat-value">{{ $categories->sum('programs_count') }}</div>
            <div class="stat-desc">tersebar di semua kategori</div>
        </div>
    </div>
</div>

<!-- Filter Section (opsional) -->
<div class="filter-card d-none d-md-block">
    <div class="filter-title">
        <i class="bi bi-funnel"></i>
        Filter Kategori
    </div>
    
    <form method="GET" action="{{ route('categories.index') }}">
        <div class="filter-grid">
            <div class="filter-group">
                <label for="search">
                    <i class="bi bi-search"></i> Cari Kategori
                </label>
                <input type="text" name="search" id="search" 
                       placeholder="Nama kategori..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="filter-group">
                <label for="has_program">
                    <i class="bi bi-bar-chart"></i> Filter Program
                </label>
                <select name="has_program" id="has_program">
                    <option value="">Semua Kategori</option>
                    <option value="yes" {{ request('has_program') == 'yes' ? 'selected' : '' }}>Memiliki Program</option>
                    <option value="no" {{ request('has_program') == 'no' ? 'selected' : '' }}>Belum Ada Program</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('categories.index') }}" class="btn-reset">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tombol Tambah Kategori (Admin Only) -->
@if(auth()->user()->isAdmin())
<div class="text-end mb-4">
    <a href="{{ route('categories.create') }}" class="btn-filter">
        <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
    </a>
</div>
@endif

<!-- Tabel untuk Desktop -->
<div class="table-container d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">No</th>
                    <th class="text-center" style="width: 80px;">Icon</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Program</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                <tr>
                    <td class="text-center">{{ $categories->firstItem() + $index }}</td>
                    <td class="text-center category-icon-cell">
                        <i class="{{ $category->icon ?? 'bi bi-tag' }}"></i>
                    </td>
                    <td>
                        <strong>{{ $category->name }}</strong>
                        <small class="slug-text">
                            <i class="bi bi-link"></i> {{ $category->slug }}
                        </small>
                    </td>
                    <td>{{ $category->description ?? '-' }}</td>
                    <td class="text-center">
                        <span class="program-badge">
                            <i class="bi bi-bar-chart"></i>
                            {{ $category->programs_count }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('categories.show', $category) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('categories.edit', $category) }}" 
                                   class="btn btn-sm btn-outline-warning" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>Belum Ada Kategori</h5>
                            <p>Mulai dengan menambahkan kategori vokasi pertama.</p>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('categories.create') }}" class="btn-filter">
                                <i class="bi bi-plus-circle"></i> Tambah Kategori
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Cards untuk Mobile -->
<div class="category-cards d-md-none">
    @forelse($categories as $category)
    <div class="category-card">
        <div class="category-header">
            <div class="category-icon">
                <i class="{{ $category->icon ?? 'bi bi-tag' }}"></i>
            </div>
            <div class="category-info">
                <div class="category-name">{{ $category->name }}</div>
                <div class="category-slug">
                    <i class="bi bi-link"></i>
                    {{ $category->slug }}
                </div>
            </div>
        </div>
        
        <div class="category-description">
            <i class="bi bi-card-text"></i>
            {{ $category->description ?? 'Tidak ada deskripsi' }}
        </div>
        
        <div class="category-footer">
            <div class="category-programs">
                <i class="bi bi-bar-chart"></i>
                {{ $category->programs_count }} Program
            </div>
            
            <div class="category-actions">
                <a href="{{ route('categories.show', $category) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                </a>
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('categories.edit', $category) }}" 
                       class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('categories.destroy', $category) }}" 
                          method="POST" 
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Yakin ingin menghapus kategori ini?')">
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
        <h5>Belum Ada Kategori</h5>
        <p>Mulai dengan menambahkan kategori vokasi pertama.</p>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('categories.create') }}" class="btn-filter">
            <i class="bi bi-plus-circle"></i> Tambah Kategori
        </a>
        @endif
    </div>
    @endforelse
</div>

<!-- ===== PAGINATION RESPONSIVE ===== -->
@if($categories->hasPages())
<div class="pagination-wrapper">
    <div class="pagination-info">
        Menampilkan {{ $categories->firstItem() }} - {{ $categories->lastItem() }} 
        dari {{ $categories->total() }} data kategori
    </div>
    
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if($categories->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-left"></i>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $categories->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
            @if($page == $categories->currentPage())
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
        @if($categories->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $categories->nextPageUrl() }}" rel="next">
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