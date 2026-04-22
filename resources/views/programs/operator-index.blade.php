@extends('layouts.dashboard')

@section('title', 'Program Vokasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-operator-programs.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h4>
        <i class="bi bi-bar-chart me-2"></i>
        Program Vokasi
    </h4>
    <p>Kelola program vokasi {{ auth()->user()->school->name }}</p>
    <div class="school-badge">
        <i class="bi bi-building"></i>
        {{ auth()->user()->school->name }}
    </div>
</div>

<!-- TOMBOL EXPORT -->
<div class="d-flex justify-content-end gap-2 mb-3">
    <button onclick="exportToExcel()" class="btn btn-success">
        <i class="bi bi-file-excel"></i> Export Excel
    </button>
    <button onclick="exportToPDF()" class="btn btn-danger">
        <i class="bi bi-file-pdf"></i> Export PDF
    </button>
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
        <div class="stat-icon active">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Program Aktif</div>
            <div class="stat-value">{{ $programs->where('status', 'active')->count() }}</div>
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
</div>

<!-- Filter Section -->
<div class="filter-card">
    <div class="filter-title">
        <i class="bi bi-funnel"></i>
        Filter Program
    </div>
    
    <form method="GET" action="{{ route('my-programs') }}">
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
                <label for="category">
                    <i class="bi bi-tags"></i> Kategori
                </label>
                <select name="category" id="category">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                            @if(!$category->is_global)
                                (Lokal)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('my-programs') }}" class="btn-reset">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tombol Tambah Program (Mobile) -->
<div class="d-md-none mb-3">
    <a href="{{ route('programs.create') }}" class="btn-filter w-100 justify-content-center">
        <i class="bi bi-plus-circle"></i> Tambah Program Baru
    </a>
</div>

<!-- Table untuk Desktop -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Program</th>
                    <th>Kategori</th>
                    <th class="text-center">Siswa</th>
                    <th class="text-center">Guru</th>
                    <th>Fasilitas</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $index => $program)
                <tr>
                    <td class="text-center">{{ $programs->firstItem() + $index }}</td>
                    <td>
                        <strong>{{ $program->program_name }}</strong>
                        @if($program->achievements)
                            <br>
                            <small class="text-success">
                                <i class="bi bi-trophy"></i> {{ Str::limit($program->achievements, 30) }}
                            </small>
                        @endif
                    </td>
                    <td>
                        {{ $program->category->name }}
                        @if(!$program->category->is_global)
                            <span class="category-badge category-local">
                                <i class="bi bi-building"></i> Lokal
                            </span>
                        @endif
                    </td>
                    <td class="text-center">{{ $program->student_count }}</td>
                    <td class="text-center">{{ $program->teacher_count ?? 0 }}</td>
                    <td>{{ Str::limit($program->facilities, 20) ?? '-' }}</td>
                    <td class="text-center">
                        @if($program->status == 'active')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('programs.show', $program) }}" 
                               class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('programs.edit', $program) }}" 
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('programs.destroy', $program) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus program ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>Belum Ada Program Vokasi</h5>
                            <p>Mulai dengan menambahkan program vokasi pertama untuk sekolah ini.</p>
                            <a href="{{ route('programs.create') }}" class="btn-add-program-sm">
                                <i class="bi bi-plus-circle"></i> Tambah Program
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
<div class="program-cards">
    @forelse($programs as $program)
    <div class="program-card">
        <div class="program-header">
            <div>
                <div class="program-title">{{ $program->program_name }}</div>
                <div class="program-category">
                    <i class="bi bi-tag"></i>
                    {{ $program->category->name }}
                    @if(!$program->category->is_global)
                        <span class="category-badge category-local">
                            <i class="bi bi-building"></i> Lokal
                        </span>
                    @endif
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
        
        @if($program->facilities)
        <div class="program-facilities">
            <i class="bi bi-tools me-2"></i>
            {{ Str::limit($program->facilities, 50) }}
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
                <div class="label">Produk</div>
                <div class="value">{{ $program->products ? '✓' : '-' }}</div>
            </div>
        </div>
        
        <div class="program-actions">
            <a href="{{ route('programs.show', $program) }}" 
               class="btn btn-sm btn-outline-primary flex-fill">
                <i class="bi bi-eye"></i> Detail
            </a>
            <a href="{{ route('programs.edit', $program) }}" 
               class="btn btn-sm btn-outline-warning flex-fill">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('programs.destroy', $program) }}" 
                  method="POST" class="d-inline flex-fill">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-sm btn-outline-danger w-100"
                        onclick="return confirm('Yakin ingin menghapus program ini?')">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>Belum Ada Program Vokasi</h5>
        <p>Mulai dengan menambahkan program vokasi pertama untuk sekolah ini.</p>
        <a href="{{ route('programs.create') }}" class="btn-add-program-sm">
            <i class="bi bi-plus-circle"></i> Tambah Program
        </a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($programs->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $programs->withQueryString()->links() }}
</div>
@endif

<!-- Tombol Tambah Program (Desktop) -->
<div class="text-end mt-4 d-none d-md-block">
    <a href="{{ route('programs.create') }}" class="btn-filter">
        <i class="bi bi-plus-circle"></i> Tambah Program Baru
    </a>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-operator-programs.js') }}"></script>
@endpush