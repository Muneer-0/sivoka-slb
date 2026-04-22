<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Login') - SiVOKA-SLB</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Custom CSS Login -->
    <link rel="stylesheet" href="{{ asset('css/sivoka-login.css') }}">
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('images/logo.png') }}" alt="SiVOKA-SLB Logo">
            <h3>SiVOKA-SLB</h3>
            <p>Sistem Informasi Vokasi SLB</p>
        </div>
        
        <div class="login-body">
            @yield('content')
        </div>
        
        <div class="login-footer">
            <div class="footer-decoration">
                <i class="bi bi-building"></i>
                <i class="bi bi-dot"></i>
                <i class="bi bi-mortarboard"></i>
                <i class="bi bi-dot"></i>
                <i class="bi bi-map"></i>
            </div>
            <p>
                <i class="bi bi-c-circle"></i> {{ date('Y') }} Bidang Pembinaan Pendidikan Khusus
                <br>
                Dinas Pendidikan Provinsi Sumatera Utara
            </p>
            <small>Sistem Informasi Vokasi SLB v1.0</small>
        </div>
    </div>
</body>
</html>