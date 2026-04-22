@extends('layouts.dashboard')

@section('title', 'Peta SLB Sumatera Utara')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-peta.css') }}">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">
        <i class="bi bi-map me-2 text-primary"></i>
        Peta SLB Sumatera Utara
    </h5>
</div>

<!-- Filter Section -->
<div class="filter-container">
    <div class="filter-header">
        <i class="bi bi-funnel"></i>
        <h5>Cari Lokasi SLB</h5>
    </div>
    
    <div class="filter-grid">
        <div class="filter-group">
            <label for="searchSchool">
                <i class="bi bi-search"></i> Cari Sekolah
            </label>
            <input type="text" id="searchSchool" placeholder="Nama SLB / Alamat / Kota..." autocomplete="off">
        </div>
        
        <!-- TAMBAHKAN INPUT NPSN DI SINI -->
        <div class="filter-group">
            <label for="searchNpsn">
                <i class="bi bi-upc-scan"></i> NPSN
            </label>
            <input type="text" id="searchNpsn" placeholder="Cari berdasarkan NPSN (8 digit)..." 
                   maxlength="8" autocomplete="off">
        </div>
        
        <div class="filter-group">
            <label for="filterCity">
                <i class="bi bi-geo-alt"></i> Filter Kota
            </label>
            <select id="filterCity">
                <option value="">Semua Kota</option>
                @foreach($schools->pluck('city')->unique()->sort() as $city)
                    <option value="{{ $city }}">{{ $city }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filterProgram">
                <i class="bi bi-bar-chart"></i> Minimal Program
            </label>
            <select id="filterProgram">
                <option value="0">Semua</option>
                <option value="1">≥ 1 Program</option>
                <option value="3">≥ 3 Program</option>
                <option value="5">≥ 5 Program</option>
            </select>
        </div>
    </div>
    
    <div class="filter-actions">
        <button class="btn-filter" id="applyFilters">
            <i class="bi bi-search"></i> Terapkan Filter
        </button>
        <button class="btn-reset" id="resetFilters">
            <i class="bi bi-arrow-repeat"></i> Reset Filter
        </button>
    </div>
</div>

<!-- Info Panel -->
<div class="info-panel" id="mapInfo">
    <div class="info-stats">
        <div class="info-stat">
            <i class="bi bi-building"></i>
            <span id="statTotal">{{ $schools->count() }}</span> SLB
        </div>
        <div class="info-stat">
            <i class="bi bi-bar-chart"></i>
            <span id="statPrograms">{{ $schools->sum('programs_count') }}</span> Program Vokasi
        </div>
        <div class="info-stat">
            <i class="bi bi-people"></i>
            <span id="statStudents">{{ number_format($schools->sum('total_students')) }}</span> Siswa
        </div>
    </div>
    <div class="info-filter" id="filterInfo">
        <i class="bi bi-info-circle"></i> Menampilkan <span id="displayCount">{{ $schools->count() }}</span> dari {{ $schools->count() }} SLB
    </div>
</div>

<!-- Map Container -->
<div id="map"></div>

<!-- Petunjuk -->
<div class="text-muted small mt-2">
    <i class="bi bi-info-circle"></i> Klik marker untuk melihat detail sekolah. Warna marker menunjukkan jumlah program:
    <span class="badge bg-danger">≥5</span>
    <span class="badge bg-warning text-dark">3-4</span>
    <span class="badge bg-warning text-dark" style="background-color: #ffc107 !important;">1-2</span>
    <span class="badge bg-secondary">0</span>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-map.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data sekolah dari server
        const schools = @json($schools);
        
        // Debug: cek apakah data sekolah mengandung NPSN
        console.log('📊 Data sekolah:', schools.length, 'sekolah');
        if (schools.length > 0) {
            console.log('📊 Contoh data pertama:', schools[0]);
            console.log('📊 NPSN di data pertama:', schools[0]?.npsn);
        }
        
        // Inisialisasi peta
        initMap(schools);
        
        // Populate city filter jika perlu
        if (typeof populateCityFilter === 'function') {
            populateCityFilter(schools);
        }
    });
</script>
@endpush