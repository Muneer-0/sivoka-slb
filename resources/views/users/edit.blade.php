@extends('layouts.dashboard')

@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">
        <i class="bi bi-person-gear me-2 text-primary"></i>
        Edit User
    </h3>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="table-container">
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Password Baru <span class="text-muted">(opsional)</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" id="role" required>
                    <option value="">Pilih Role</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pimpinan" {{ old('role', $user->role) == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                    <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operator</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-8 mb-3" id="school-field" style="{{ old('role', $user->role) == 'operator' ? '' : 'display: none;' }}">
                <label class="form-label">SLB <span class="text-danger" id="school-required" style="display: {{ old('role', $user->role) == 'operator' ? 'inline' : 'none' }};">*</span></label>
                <select name="school_id" class="form-select @error('school_id') is-invalid @enderror">
                    <option value="">Pilih SLB</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id', $user->school_id) == $school->id ? 'selected' : '' }}>
                            {{ $school->name }} ({{ $school->city }})
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Wajib diisi jika role = Operator</small>
                @error('school_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const schoolField = document.getElementById('school-field');
        const schoolRequired = document.getElementById('school-required');
        const schoolSelect = document.querySelector('select[name="school_id"]');
        
        if (this.value === 'operator') {
            schoolField.style.display = 'block';
            schoolRequired.style.display = 'inline';
            schoolSelect.setAttribute('required', 'required');
        } else {
            schoolField.style.display = 'none';
            schoolRequired.style.display = 'none';
            schoolSelect.removeAttribute('required');
        }
    });
</script>
@endpush
@endsection