@extends('layouts.dashboard')

@section('title', 'Edit Program Vokasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">
        <i class="bi bi-pencil-square me-2 text-primary"></i>
        Edit Program Vokasi
    </h3>
    @if(auth()->user()->isOperator())
        <a href="{{ route('my-programs') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    @else
        <a href="{{ route('programs.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    @endif
</div>

<div class="table-container">
    <form action="{{ route('programs.update', $program) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">SLB</label>
                <input type="text" class="form-control" value="{{ $program->school->name }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $program->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Nama Program <span class="text-danger">*</span></label>
                <input type="text" name="program_name" class="form-control @error('program_name') is-invalid @enderror" 
                       value="{{ old('program_name', $program->program_name) }}" required>
                @error('program_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Deskripsi Program</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="3">{{ old('description', $program->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Jumlah Siswa <span class="text-danger">*</span></label>
                <input type="number" name="student_count" class="form-control @error('student_count') is-invalid @enderror" 
                       value="{{ old('student_count', $program->student_count) }}" min="0" required>
                @error('student_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Jumlah Guru</label>
                <input type="number" name="teacher_count" class="form-control @error('teacher_count') is-invalid @enderror" 
                       value="{{ old('teacher_count', $program->teacher_count ?? 0) }}" min="0">
                @error('teacher_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status', $program->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', $program->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Fasilitas</label>
                <textarea name="facilities" class="form-control @error('facilities') is-invalid @enderror" 
                          rows="2">{{ old('facilities', $program->facilities) }}</textarea>
                @error('facilities')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Produk Unggulan</label>
                <textarea name="products" class="form-control @error('products') is-invalid @enderror" 
                          rows="2">{{ old('products', $program->products) }}</textarea>
                @error('products')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Prestasi</label>
                <textarea name="achievements" class="form-control @error('achievements') is-invalid @enderror" 
                          rows="2">{{ old('achievements', $program->achievements) }}</textarea>
                @error('achievements')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                @if(auth()->user()->isOperator())
                    <a href="{{ route('my-programs') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                @else
                    <a href="{{ route('programs.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection