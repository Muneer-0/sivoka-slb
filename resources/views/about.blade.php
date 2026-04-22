@extends('layouts.dashboard')

@section('title', 'Tentang SiVOKA-SLB')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="table-container text-center">
            <div class="mb-4">
                <i class="bi bi-building text-primary" style="font-size: 5rem;"></i>
                <h2 class="mt-3">SiVOKA-SLB</h2>
                <h5 class="text-muted">Sistem Informasi Vokasi Sekolah Luar Biasa</h5>
            </div>
            
            <hr class="my-4">
            
            <div class="row text-start">
                <div class="col-md-6">
                    <h5><i class="bi bi-info-circle me-2 text-primary"></i>Tentang Aplikasi</h5>
                    <p>SiVOKA-SLB adalah sistem informasi yang dikembangkan untuk memetakan program vokasi di Sekolah Luar Biasa (SLB) se-Provinsi Sumatera Utara. Sistem ini membantu Bidang Pembinaan Pendidikan Khusus dalam mengelola data program vokasi, memantau perkembangan, serta menyusun laporan berbasis data.</p>
                    
                    <h5 class="mt-4"><i class="bi bi-gear me-2 text-primary"></i>Fitur Utama</h5>
                    <ul>
                        <li>Pemetaan program vokasi per SLB</li>
                        <li>Dashboard visual dengan grafik interaktif</li>
                        <li>Monitoring kelengkapan data</li>
                        <li>Laporan export Excel & PDF</li>
                        <li>Filter data multi-kriteria</li>
                        <li>Manajemen multi-level user</li>
                    </ul>
                </div>
                
                <div class="col-md-6">
                    <h5><i class="bi bi-people me-2 text-primary"></i>Pengembang</h5>
                    <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Jabatan:</strong> Penata Kelola Sistem dan Teknologi Informasi</p>
                    <p><strong>Unit Kerja:</strong> Bidang Pembinaan Pendidikan Khusus<br>Dinas Pendidikan Provinsi Sumatera Utara</p>
                    
                    <h5 class="mt-4"><i class="bi bi-calendar me-2 text-primary"></i>Informasi Sistem</h5>
                    <p><strong>Versi:</strong> 1.0.0</p>
                    <p><strong>Tahun Pengembangan:</strong> 2026</p>
                    <p><strong>Framework:</strong> Laravel 10 + Bootstrap 5</p>
                    <p><strong>Database:</strong> MySQL</p>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Aplikasi ini dikembangkan dalam rangka aktualisasi Latsar CPNS Golongan III Tahun 2026.
            </div>
            
            <div class="text-muted mt-4">
                <small>© 2026 Bidang Pembinaan Pendidikan Khusus<br>Dinas Pendidikan Provinsi Sumatera Utara</small>
            </div>
        </div>
    </div>
</div>
@endsection