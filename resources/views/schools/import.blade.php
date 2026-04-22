@extends('layouts.dashboard')

@push('styles')
<style>
    .template-table {
        font-size: 12px;
    }
    .template-table th {
        background: #e7f1ff;
        font-weight: 600;
        white-space: nowrap;
    }
    .template-table td {
        font-family: monospace;
    }
    .required-star {
        color: #dc3545;
    }
    .optional-star {
        color: #6c757d;
    }
    .preview-box {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 15px;
        overflow-x: auto;
    }
    .info-card-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
</style>
@endpush

@section('title', 'Import Data SLB dari Excel')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-file-earmark-excel me-2 text-primary"></i>
            Import Data SLB dari Excel
        </h4>
        <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Form Upload -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-cloud-upload me-2"></i> Upload File Excel
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            @if(session('errors_detail'))
                                <ul class="mt-2 mb-0 small">
                                    @foreach(session('errors_detail') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-x-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('schools.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="file" class="form-label fw-bold">
                                <i class="bi bi-file-earmark-excel me-1"></i> Pilih File Excel
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file" accept=".xlsx,.xls,.csv" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i> 
                                Format: .xlsx, .xls, .csv | Maksimal ukuran: 5MB
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('schools.import.template') }}" class="btn btn-success">
                                <i class="bi bi-download me-1"></i> Download Template CSV
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload me-1"></i> Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Informasi -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-info-circle me-2"></i> Informasi Import
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="info-card-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Update atau Insert Data</h6>
                            <small class="text-muted">Jika NPSN sudah ada, data akan di-update</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="info-card-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Otomatis Buat User Operator</h6>
                            <small class="text-muted">Setiap SLB akan memiliki akun operator</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="info-card-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Koordinat Otomatis (Default)</h6>
                            <small class="text-muted">Jika latitude/longitude kosong, akan diisi otomatis berdasarkan kota</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORMAT TEMPLATE -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-table me-2"></i> Format Template CSV yang Benar
                </div>
                <div class="card-body">
                    <div class="preview-box">
                        <div class="table-responsive">
                            <table class="table table-bordered template-table">
                                <thead>
                                    <tr>
                                        <th>npsn <span class="required-star">*</span></th>
                                        <th>name <span class="required-star">*</span></th>
                                        <th>address</th>
                                        <th>village</th>
                                        <th>district</th>
                                        <th>city <span class="required-star">*</span></th>
                                        <th>province</th>
                                        <th>phone</th>
                                        <th>email</th>
                                        <th>headmaster</th>
                                        <th>status</th>
                                        <th>accreditation</th>
                                        <th>latitude <span class="optional-star">(opsional)</span></th>
                                        <th>longitude <span class="optional-star">(opsional)</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>10207451</code></td>
                                        <td><code>SLB B Karya Murni</code></td>
                                        <td><code>Jl. Karya No. 45</code></td>
                                        <td><code>Pulo Brayan</code></td>
                                        <td><code>Medan Barat</code></td>
                                        <td><code>Medan</code></td>
                                        <td><code>Sumatera Utara</code></td>
                                        <td><code>061-1234567</code></td>
                                        <td><code>slbbkaryamurni@sch.id</code></td>
                                        <td><code>Dra. Siti Aminah, M.Pd</code></td>
                                        <td><code>swasta</code></td>
                                        <td><code>A</code></td>
                                        <td><code>3.5952</code></td>
                                        <td><code>98.6722</code></td>
                                    </tr>
                                    <tr>
                                        <td><code>10207452</code></td>
                                        <td><code>SLB N Pembina Medan</code></td>
                                        <td><code>Jl. Pendidikan No. 10</code></td>
                                        <td><code>Petisah Tengah</code></td>
                                        <td><code>Medan Petisah</code></td>
                                        <td><code>Medan</code></td>
                                        <td><code>Sumatera Utara</code></td>
                                        <td><code>061-7654321</code></td>
                                        <td><code>slbnpembinamedan@sch.id</code></td>
                                        <td><code>Drs. Ahmad Yani, M.Si</code></td>
                                        <td><code>negeri</code></td>
                                        <td><code>A</code></td>
                                        <td><code>3.5890</code></td>
                                        <td><code>98.6780</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row g-2 mt-3">
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <small class="text-muted">📌 <strong>Keterangan:</strong></small>
                                    <small class="d-block"><span class="required-star">*</span> Wajib diisi</small>
                                    <small class="d-block"><span class="optional-star">(opsional)</span> Tidak wajib</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <small class="text-muted">🏷️ <strong>Status:</strong></small>
                                    <small class="d-block">negeri / swasta</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <small class="text-muted">⭐ <strong>Akreditasi:</strong></small>
                                    <small class="d-block">A, B, C (opsional)</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-light">
                                    <small class="text-muted">🔑 <strong>NPSN:</strong></small>
                                    <small class="d-block">8 digit angka</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="bi bi-geo-alt me-1"></i>
                            <strong>Informasi Koordinat:</strong> 
                            Kolom <code>latitude</code> dan <code>longitude</code> bersifat <strong>opsional (tidak wajib)</strong>. 
                            Jika dikosongkan, sistem akan mengisi koordinat default berdasarkan kota.
                        </div>
                        
                        <div class="alert alert-warning mt-2 mb-0 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Catatan Penting:</strong> 
                            Jangan sertakan kolom <code>id</code>, <code>created_at</code>, <code>updated_at</code> di file Excel Anda.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi User Operator -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-person-badge me-2"></i> Informasi User Operator
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Setiap import data SLB akan otomatis membuat/mengupdate user operator dengan spesifikasi:</strong>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-white">
                                    <small class="text-muted d-block">👤 Username</small>
                                    <code class="fw-bold">NPSN (8 digit)</code>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-white">
                                    <small class="text-muted d-block">🔒 Password</small>
                                    <code class="fw-bold">password123</code>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-white">
                                    <small class="text-muted d-block">👔 Role</small>
                                    <code class="fw-bold">Operator</code>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2 bg-white">
                                    <small class="text-muted d-block">📧 Email</small>
                                    <code class="fw-bold">operator_{npsn}@sivoka.local</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection