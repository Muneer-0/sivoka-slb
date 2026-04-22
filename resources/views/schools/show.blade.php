@extends('layouts.dashboard')

@section('title', 'Detail SLB')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-detail.css') }}">
<link rel="stylesheet" href="{{ asset('css/sivoka-action-icons.css') }}">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="mb-0">
        <i class="bi bi-building me-2 text-primary"></i>
        Detail SLB
    </h4>
    <div class="d-flex gap-2">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('schools.edit', $school) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        @endif
        
        <!-- TOMBOL KEMBALI -->
        @if(auth()->user()->isOperator())
            <a href="{{ route('my-school') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        @elseif(auth()->user()->isPimpinan())
            <a href="{{ route('programs.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        @else
            <button onclick="window.history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Informasi Umum -->
    <div class="col-md-6">
        <div class="info-card">
            <div class="info-header">
                <i class="bi bi-info-circle"></i>
                <h5>Informasi Umum</h5>
            </div>
            
            <div class="info-row">
                <span class="info-label">NPSN</span>
                <span class="info-value">
                    <i class="bi bi-upc-scan"></i>
                    <strong>{{ $school->npsn }}</strong>
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Nama SLB</span>
                <span class="info-value">
                    <i class="bi bi-building"></i>
                    {{ $school->name }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Alamat</span>
                <span class="info-value">
                    <i class="bi bi-geo-alt"></i>
                    {{ $school->address }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Kecamatan</span>
                <span class="info-value">
                    <i class="bi bi-pin-map"></i>
                    {{ $school->district }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Kota/Kab</span>
                <span class="info-value">
                    <i class="bi bi-city"></i>
                    {{ $school->city }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Provinsi</span>
                <span class="info-value">
                    <i class="bi bi-globe2"></i>
                    {{ $school->province }}
                </span>
            </div>
        </div>
    </div>
    
    <!-- Informasi Kontak & Akreditasi -->
    <div class="col-md-6">
        <div class="info-card">
            <div class="info-header">
                <i class="bi bi-person-badge"></i>
                <h5>Kontak & Akreditasi</h5>
            </div>
            
            <div class="info-row">
                <span class="info-label">Telepon</span>
                <span class="info-value">
                    <i class="bi bi-telephone"></i>
                    {{ $school->phone ?? '-' }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">
                    <i class="bi bi-envelope"></i>
                    {{ $school->email ?? '-' }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Kepala Sekolah</span>
                <span class="info-value">
                    <i class="bi bi-person"></i>
                    {{ $school->headmaster ?? '-' }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if($school->status == 'negeri')
                        <span class="badge-custom badge-negeri">
                            <i class="bi bi-building"></i> Negeri
                        </span>
                    @else
                        <span class="badge-custom badge-swasta">
                            <i class="bi bi-building"></i> Swasta
                        </span>
                    @endif
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Akreditasi</span>
                <span class="info-value">
                    @if($school->accreditation)
                        <span class="badge-custom badge-akreditasi">
                            <i class="bi bi-award"></i> {{ $school->accreditation }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Program Vokasi -->
<div class="row mt-4">
    <div class="col-12">
        <div class="info-card">
            <div class="info-header">
                <i class="bi bi-bar-chart"></i>
                <h5>Program Vokasi di SLB ini</h5>
                @if(auth()->user()->isOperator())
                    <a href="{{ route('programs.create') }}" class="btn btn-sm btn-primary ms-auto">
                        <i class="bi bi-plus-circle"></i> Tambah Program
                    </a>
                @endif
            </div>
            
            <!-- Tabel untuk Desktop -->
            <div class="program-table-desktop">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Program</th>
                                <th>Kategori</th>
                                <th class="text-center">Siswa</th>
                                <th class="text-center">Guru</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($school->programs as $index => $program)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $program->program_name }}</strong>
                                    @if($program->achievements)
                                        <br>
                                        <small class="text-success">
                                            <i class="bi bi-trophy"></i> {{ Str::limit($program->achievements, 30) }}
                                        </small>
                                    @endif
                                </td>
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
                                    <div class="action-icons">
                                        <a href="{{ route('programs.show', $program) }}" 
                                           class="btn-icon btn-icon-primary" 
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && auth()->user()->school_id == $program->school_id))
                                            <a href="{{ route('programs.edit', $program) }}" 
                                               class="btn-icon btn-icon-warning" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('programs.destroy', $program) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn-icon btn-icon-danger" 
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada program vokasi di SLB ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Card untuk Mobile -->
            <div class="program-card-mobile-container">
                @forelse($school->programs as $program)
                <div class="program-card-mobile">
                    <div class="program-card-header">
                        <div>
                            <div class="program-card-title">{{ $program->program_name }}</div>
                            <div class="program-card-category">
                                <i class="bi bi-tag"></i> {{ $program->category->name }}
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
                        <div class="mb-2 text-success small">
                            <i class="bi bi-trophy"></i> {{ Str::limit($program->achievements, 50) }}
                        </div>
                    @endif
                    
                    <div class="program-card-stats">
                        <div class="stat-item">
                            <div class="label">Siswa</div>
                            <div class="value">{{ $program->student_count }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Guru</div>
                            <div class="value">{{ $program->teacher_count ?? 0 }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Fasilitas</div>
                            <div class="value">{{ $program->facilities ? '✓' : '-' }}</div>
                        </div>
                    </div>
                    
                    <div class="program-card-actions">
                        <a href="{{ route('programs.show', $program) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        
                        @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && auth()->user()->school_id == $program->school_id))
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
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Belum ada program vokasi di SLB ini
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-action-icons.js') }}"></script>
@endpush