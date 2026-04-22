@extends('layouts.dashboard')

@section('title', 'Import User')

@push('styles')
<style>
    .import-container {
        max-width: 600px;
        margin: 0 auto;
    }
    .template-card {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 15px;
        margin-top: 20px;
    }
    .template-card pre {
        background: #fff;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        overflow-x: auto;
    }
</style>
@endpush

@section('content')
<div class="import-container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-file-earmark-excel me-2"></i>
            Import User dari Excel
        </div>
        
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Petunjuk:</strong>
                <ul class="mb-0 mt-2">
                    <li>File harus dalam format <strong>.xlsx, .xls, atau .csv</strong></li>
                    <li>Maksimal ukuran file <strong>5 MB</strong></li>
                    <li>Kolom yang wajib diisi: <strong>name, email, role</strong></li>
                    <li>Role yang tersedia: <strong>admin, pimpinan, operator</strong></li>
                    <li>Untuk role operator, wajib mengisi <strong>npsn</strong> sekolah yang valid</li>
                    <li>Password default: <strong>password123</strong> (bisa diisi sendiri)</li>
                </ul>
            </div>
            
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
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
                    <div class="form-text">Format file: .xlsx, .xls, .csv | Maks: 5MB</div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('users.import.template') }}" class="btn btn-secondary">
                        <i class="bi bi-download me-1"></i> Download Template CSV
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Import User
                    </button>
                </div>
            </form>
            
            <div class="template-card">
                <h6><i class="bi bi-question-circle me-1"></i> Format Template CSV</h6>
                <pre>name,email,role,npsn,password
Admin Sistem,admin@sivoka.com,admin,,password123
Kepala Bidang,kabid@sivoka.com,pimpinan,,password123
Operator SLB,operator@slb.sch.id,operator,10207451,password123</pre>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle"></i> 
                    Kolom <strong>password</strong> opsional, jika kosong akan menggunakan default "password123"
                </p>
            </div>
        </div>
        
        <div class="card-footer text-muted">
            <i class="bi bi-arrow-left me-1"></i>
            <a href="{{ route('users.index') }}">Kembali ke Manajemen User</a>
        </div>
    </div>
</div>
@endsection