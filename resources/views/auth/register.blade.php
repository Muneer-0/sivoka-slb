@extends('layouts.login')

@section('title', 'Register')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name') }}" required>
        </div>
        @error('name')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" id="roleSelect" onchange="toggleIdentifierField()">
            <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator Sekolah</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="pimpinan" {{ old('role') == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
        </select>
    </div>

    <!-- Field untuk NPSN (Operator) -->
    <div class="mb-3" id="field-npsn">
        <label for="npsn" class="form-label">NPSN Sekolah</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-building"></i></span>
            <input type="text" class="form-control @error('npsn') is-invalid @enderror" 
                   id="npsn" name="npsn" value="{{ old('npsn') }}" 
                   placeholder="8 digit NPSN" maxlength="8">
        </div>
        @error('npsn')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
        <small class="text-muted">Khusus untuk Operator (8 digit)</small>
    </div>

    <!-- Field untuk NIP (Admin/Pimpinan) -->
    <div class="mb-3" id="field-nip" style="display: none;">
        <label for="nip" class="form-label">NIP</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
            <input type="text" class="form-control @error('nip') is-invalid @enderror" 
                   id="nip" name="nip" value="{{ old('nip') }}" 
                   placeholder="Nomor Induk Pegawai">
        </div>
        @error('nip')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
        <small class="text-muted">Khusus untuk Admin/Pimpinan</small>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email (Opsional)</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email') }}">
        </div>
        @error('email')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" required>
        </div>
        @error('password')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password-confirm" class="form-label">Konfirmasi Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" 
                   id="password-confirm" name="password_confirmation" required>
        </div>
    </div>

    <button type="submit" class="btn btn-login">
        <i class="bi bi-person-plus me-2"></i>Register
    </button>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none">
            <i class="bi bi-box-arrow-in-right"></i> Already have an account? Login
        </a>
    </div>
</form>

<script>
    function toggleIdentifierField() {
        const role = document.getElementById('roleSelect').value;
        const fieldNpsn = document.getElementById('field-npsn');
        const fieldNip = document.getElementById('field-nip');
        const npsnInput = document.getElementById('npsn');
        const nipInput = document.getElementById('nip');
        
        if (role === 'operator') {
            fieldNpsn.style.display = 'block';
            fieldNip.style.display = 'none';
            npsnInput.setAttribute('required', 'required');
            nipInput.removeAttribute('required');
        } else {
            fieldNpsn.style.display = 'none';
            fieldNip.style.display = 'block';
            npsnInput.removeAttribute('required');
            nipInput.setAttribute('required', 'required');
        }
    }
    
    // Jalankan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        toggleIdentifierField();
    });
</script>
@endsection