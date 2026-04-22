@extends('layouts.dashboard')

@section('title', 'Tambah Program Vokasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-program-create.css') }}">
@endpush

@section('content')
<!-- HAPUS container dan row, biar full width -->
<div class="program-create-wrapper">

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-plus-circle me-2"></i> Tambah Program Vokasi
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('programs.store') }}" id="programForm">
                @csrf

                <!-- SLB -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-building"></i> Sekolah Luar Biasa (SLB)
                    </label>
                    @if(auth()->user()->isOperator())
                        <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
                        <input type="text" class="form-control" value="{{ auth()->user()->school->name }}" readonly>
                    @else
                        <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                            <option value="">Pilih SLB</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ Str::limit($school->name, 50) }} ({{ $school->city }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('school_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kategori dengan Tombol Tambah -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-tags"></i> Kategori Program
                    </label>
                    <div class="category-group">
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    data-is-global="{{ $category->is_global ? '1' : '0' }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} 
                                    @if(!$category->is_global)
                                        (Lokal)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if(auth()->user()->isOperator())
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#newCategoryModal" title="Tambah Kategori Baru">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endif
                    </div>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        @if(auth()->user()->isOperator())
                            <i class="bi bi-info-circle"></i> 
                            Klik tombol <strong>+</strong> untuk menambah kategori khusus untuk sekolah Anda
                        @else
                            <i class="bi bi-info-circle"></i> 
                            Pilih kategori program vokasi yang sesuai
                        @endif
                    </div>
                </div>

                <!-- Nama Program -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-bar-chart"></i> Nama Program <span class="text-danger">*</span>
                    </label>
                    <input id="program_name" type="text" class="form-control @error('program_name') is-invalid @enderror" 
                           name="program_name" value="{{ old('program_name') }}" 
                           placeholder="Contoh: Pelatihan Membatik, Kursus Menjahit, dll" required>
                    @error('program_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Tuliskan nama program vokasi secara spesifik dan jelas</div>
                </div>

                <!-- Deskripsi -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-journal-text"></i> Deskripsi Program
                    </label>
                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                              name="description" rows="3" placeholder="Jelaskan tentang program vokasi ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row untuk Siswa & Guru -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-people"></i> Jumlah Siswa <span class="text-danger">*</span>
                            </label>
                            <input id="student_count" type="number" class="form-control @error('student_count') is-invalid @enderror" 
                                   name="student_count" value="{{ old('student_count', 0) }}" required min="0">
                            @error('student_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-person-badge"></i> Jumlah Guru
                            </label>
                            <input id="teacher_count" type="number" class="form-control @error('teacher_count') is-invalid @enderror" 
                                   name="teacher_count" value="{{ old('teacher_count', 0) }}" min="0">
                            @error('teacher_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Fasilitas -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-tools"></i> Fasilitas
                    </label>
                    <textarea id="facilities" class="form-control @error('facilities') is-invalid @enderror" 
                              name="facilities" rows="2" placeholder="Contoh: 5 mesin jahit, 3 oven, 2 komputer, ruang praktik">{{ old('facilities') }}</textarea>
                    @error('facilities')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Sebutkan fasilitas yang tersedia untuk program ini</div>
                </div>

                <!-- Produk Unggulan -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-box-seam"></i> Produk Unggulan
                    </label>
                    <textarea id="products" class="form-control @error('products') is-invalid @enderror" 
                              name="products" rows="2" placeholder="Contoh: kue kering, batik, tas rajut, kerajinan kayu">{{ old('products') }}</textarea>
                    @error('products')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Prestasi -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-trophy"></i> Prestasi
                    </label>
                    <textarea id="achievements" class="form-control @error('achievements') is-invalid @enderror" 
                              name="achievements" rows="2" placeholder="Contoh: Juara 1 FLS2N 2025, Juara 2 Lomba Keterampilan Siswa">{{ old('achievements') }}</textarea>
                    @error('achievements')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('my-programs') }}" class="btn-cancel">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-save me-1"></i> Simpan Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori Lokal -->
<div class="modal fade" id="newCategoryModal" tabindex="-1" aria-labelledby="newCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCategoryModalLabel">
                    <i class="bi bi-tag me-2"></i>Tambah Kategori Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Kategori ini hanya akan tersedia untuk <strong>sekolah Anda sendiri</strong>.
                </div>
                
                <div class="mb-3">
                    <label for="new_category_name" class="form-label fw-bold">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="new_category_name" 
                           placeholder="Contoh: Ecoprint, Kriya Bambu, Digital Marketing">
                    <small class="text-muted">Nama kategori akan otomatis diubah menjadi format slug</small>
                </div>
                
                <div class="mb-3">
                    <label for="new_category_description" class="form-label">Deskripsi (Opsional)</label>
                    <textarea class="form-control" id="new_category_description" rows="2" 
                              placeholder="Jelaskan kategori ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="bi bi-save me-1"></i> Simpan Kategori
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-program-create.js') }}"></script>
@endpush