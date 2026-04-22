@extends('layouts.login')

@section('title', 'Login')

@section('content')

<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="mb-3">
        <label for="identifier" class="form-label">Email / NIP / NPSN</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
            <input type="text" class="form-control @error('identifier') is-invalid @enderror" 
                   id="identifier" name="identifier" value="{{ old('identifier') }}" 
                   placeholder="Masukkan Email / NIP / NPSN" 
                   required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" 
                   placeholder="Masukkan password" required>
        </div>
        @error('password')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">Remember Me</label>
    </div>

    <button type="submit" class="btn btn-login w-100">
        <i class="bi bi-box-arrow-in-right me-2"></i>Login
    </button>

    <div class="text-center mt-3">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-decoration-none small">
                <i class="bi bi-question-circle"></i> Forgot Your Password?
            </a>
        @endif
    </div>
</form>

<!-- Script untuk Toast Alert -->
<script>
    // Fungsi untuk menampilkan toast di pojok kanan atas
    function showToast(message, type = 'error') {
        // Buat elemen toast
        const toast = document.createElement('div');
        toast.className = 'login-toast';
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi ${type === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Warna background berdasarkan jenis
        if (type === 'error') {
            toast.style.background = '#dc3545';
        } else {
            toast.style.background = '#28a745';
        }
        
        document.body.appendChild(toast);
        
        // Animasi masuk
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        // Hapus setelah 3 detik
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Tampilkan toast jika ada error
    @if($errors->any())
        showToast('Email/NIP/NPSN atau password salah', 'error');
    @endif

    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
</script>

<style>
    /* Toast Style */
    .login-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: #dc3545;
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        font-size: 14px;
        font-weight: 500;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 350px;
        width: auto;
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .toast-content i {
        font-size: 1.2rem;
    }
    
    .toast-content span {
        flex: 1;
    }
    
    /* Responsive untuk mobile */
    @media (max-width: 576px) {
        .login-toast {
            left: 20px;
            right: 20px;
            max-width: none;
            top: 15px;
        }
    }
</style>

@endsection