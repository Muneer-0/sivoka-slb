@extends('layouts.dashboard')

@section('title', 'Edit Data SLB')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">
        <i class="bi bi-pencil-square me-2 text-primary"></i>
        Edit Data SLB
    </h3>
    <a href="{{ route('schools.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="table-container">
    <form action="{{ route('schools.update', $school) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">NPSN <span class="text-danger">*</span></label>
                <input type="text" name="npsn" class="form-control @error('npsn') is-invalid @enderror" 
                       value="{{ old('npsn', $school->npsn) }}" maxlength="8" required>
                @error('npsn')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Nama SLB <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $school->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                          rows="2" required>{{ old('address', $school->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" 
                       value="{{ old('district', $school->district) }}" required>
                @error('district')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Kota/Kab <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                       value="{{ old('city', $school->city) }}" required>
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Kepala Sekolah</label>
                <input type="text" name="headmaster" class="form-control @error('headmaster') is-invalid @enderror" 
                       value="{{ old('headmaster', $school->headmaster) }}">
                @error('headmaster')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">Pilih Status</option>
                    <option value="negeri" {{ old('status', $school->status) == 'negeri' ? 'selected' : '' }}>Negeri</option>
                    <option value="swasta" {{ old('status', $school->status) == 'swasta' ? 'selected' : '' }}>Swasta</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Akreditasi</label>
                <select name="accreditation" class="form-select @error('accreditation') is-invalid @enderror">
                    <option value="">Pilih Akreditasi</option>
                    <option value="A" {{ old('accreditation', $school->accreditation) == 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ old('accreditation', $school->accreditation) == 'B' ? 'selected' : '' }}>B</option>
                    <option value="C" {{ old('accreditation', $school->accreditation) == 'C' ? 'selected' : '' }}>C</option>
                </select>
                @error('accreditation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                       value="{{ old('phone', $school->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Koordinat -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <h6 class="text-primary mb-3">
                    <i class="bi bi-geo-alt me-1"></i> Koordinat Lokasi
                </h6>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Latitude</label>
                <input type="number" step="any" name="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                       value="{{ old('latitude', $school->latitude) }}" placeholder="Contoh: 3.5952">
                @error('latitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Longitude</label>
                <input type="number" step="any" name="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                       value="{{ old('longitude', $school->longitude) }}" placeholder="Contoh: 98.6722">
                @error('longitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('schools.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection