@extends('layouts.dashboard')

@section('title', 'Laporan Program Vokasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-reports.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h4>
        <i class="bi bi-file-text me-2"></i>
        Laporan Program Vokasi
    </h4>
    <p>Rekapitulasi program vokasi di seluruh SLB Sumatera Utara</p>
    
    <div class="header-stats">
        <div class="header-stat">
            <i class="bi bi-calendar"></i>
            {{ date('d F Y') }}
        </div>
        <div class="header-stat">
            <i class="bi bi-building"></i>
            {{ $stats['totalSchools'] ?? 0 }} SLB
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-card">
    <div class="filter-title">
        <i class="bi bi-funnel"></i>
        Filter Laporan
    </div>
    
    <form method="GET" action="{{ route('reports.index') }}">
        <div class="filter-grid">
            <div class="filter-group">
                <label for="city">
                    <i class="bi bi-geo-alt"></i> Kota/Kab
                </label>
                <select name="city" id="city">
                    <option value="">Semua Kota</option>
                    @foreach($filters['cities'] as $city)
                        <option value="{{ $city }}" {{ $filters['selectedCity'] == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="category">
                    <i class="bi bi-tags"></i> Kategori
                </label>
                <select name="category" id="category">
                    <option value="">Semua Kategori</option>
                    @foreach($filters['categories'] as $category)
                        <option value="{{ $category->id }}" {{ $filters['selectedCategory'] == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="school_id">
                    <i class="bi bi-building"></i> SLB
                </label>
                <select name="school_id" id="school_id">
                    <option value="">Semua SLB</option>
                    @foreach($filters['schools'] as $school)
                        <option value="{{ $school->id }}" {{ $filters['selectedSchool'] == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date_from">
                    <i class="bi bi-calendar-start"></i> Tanggal Mulai
                </label>
                <input type="date" name="date_from" id="date_from" value="{{ $filters['dateFrom'] }}">
            </div>
            
            <div class="filter-group">
                <label for="date_to">
                    <i class="bi bi-calendar-end"></i> Tanggal Akhir
                </label>
                <input type="date" name="date_to" id="date_to" value="{{ $filters['dateTo'] }}">
            </div>
        </div>
        
        <div class="filter-actions">
            <button type="submit" class="btn-filter">
                <i class="bi bi-search"></i> Tampilkan Laporan
            </button>
            <a href="{{ route('reports.index') }}" class="btn-reset">
                <i class="bi bi-arrow-repeat"></i> Reset
            </a>
            <button type="button" class="btn-excel" 
                    onclick="window.location.href='{{ route('reports.export.excel') }}' + window.location.search">
                <i class="bi bi-file-excel"></i> Export Excel
            </button>
            <button type="button" class="btn-pdf" 
                    onclick="window.location.href='{{ route('reports.export.pdf') }}' + window.location.search">
                <i class="bi bi-file-pdf"></i> Export PDF
            </button>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card slb">
        <i class="bi bi-building stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Total SLB</div>
            <div class="stat-value">{{ $stats['totalSchools'] ?? 0 }}</div>
            <div class="stat-desc">dengan program vokasi</div>
        </div>
    </div>
    
    <div class="stat-card program">
        <i class="bi bi-bar-chart stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Program Vokasi</div>
            <div class="stat-value">{{ $stats['totalPrograms'] ?? 0 }}</div>
            <div class="stat-desc">total program</div>
        </div>
    </div>
    
    <div class="stat-card siswa">
        <i class="bi bi-people stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ number_format($stats['totalStudents'] ?? 0) }}</div>
            <div class="stat-desc">peserta vokasi</div>
        </div>
    </div>
    
    <div class="stat-card kategori">
        <i class="bi bi-tags stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">Kategori</div>
            <div class="stat-value">{{ $stats['totalCategories'] ?? 0 }}</div>
            <div class="stat-desc">aktif digunakan</div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-grid">
    <!-- Program per Kategori -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-bar-chart"></i>
            <h5>Program per Kategori</h5>
        </div>
        <div class="chart-container">
            <canvas id="categoryChart"></canvas>
        </div>
        <div class="chart-summary">
            <div class="chart-summary-item">
                <span class="chart-summary-label">Total Kategori:</span>
                <span class="chart-summary-value">{{ $perCategory->count() }}</span>
            </div>
            <div class="chart-summary-item">
                <span class="chart-summary-label">Total Program:</span>
                <span class="chart-summary-value">{{ $perCategory->sum('programs_count') }}</span>
            </div>
        </div>
    </div>
    
    <!-- Program per Kota -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-pie-chart"></i>
            <h5>Program per Kota</h5>
        </div>
        <div class="chart-container">
            <canvas id="cityChart"></canvas>
        </div>
        <div class="chart-summary">
            <div class="chart-summary-item">
                <span class="chart-summary-label">Total Kota:</span>
                <span class="chart-summary-value">{{ $perCity->count() }}</span>
            </div>
            <div class="chart-summary-item">
                <span class="chart-summary-label">Total Program:</span>
                <span class="chart-summary-value">{{ $perCity->sum('total') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Summary Tables -->
<div class="summary-grid">
    <!-- Ringkasan per Kategori -->
    <div class="summary-card">
        <div class="summary-header">
            <i class="bi bi-tags"></i>
            <h5>Program per Kategori</h5>
        </div>
        <div class="summary-content">
            @foreach($perCategory as $item)
            <div class="summary-item">
                <span class="summary-label">{{ $item->name }}</span>
                <span class="summary-value">
                    <span class="badge-program">
                        <i class="bi bi-bar-chart"></i> {{ $item->programs_count }}
                    </span>
                </span>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Ringkasan per Kota -->
    <div class="summary-card">
        <div class="summary-header">
            <i class="bi bi-geo-alt"></i>
            <h5>Program per Kota</h5>
        </div>
        <div class="summary-content">
            @foreach($perCity as $item)
            <div class="summary-item">
                <span class="summary-label">{{ $item->city }}</span>
                <span class="summary-value">
                    <span class="badge-city">
                        <i class="bi bi-bar-chart"></i> {{ $item->total }}
                    </span>
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 10 SLB dengan Program Terbanyak -->
<div class="table-card">
    <div class="section-title">
        <i class="bi bi-trophy"></i>
        <h5>10 SLB dengan Program Vokasi Terbanyak</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama SLB</th>
                    <th>Kota/Kab</th>
                    <th class="text-center">Program</th>
                    <th class="text-center">Siswa</th>
                    <th class="text-center">Akreditasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSchools as $index => $school)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $school->name }}</td>
                    <td>{{ $school->city }}</td>
                    <td class="text-center">
                        <span class="badge-program">
                            <i class="bi bi-bar-chart"></i> {{ $school->programs_count }}
                        </span>
                    </td>
                    <td class="text-center">{{ number_format($school->programs_sum_student_count ?? 0) }}</td>
                    <td class="text-center">
                        @if($school->accreditation)
                            <span class="badge bg-info">{{ $school->accreditation }}</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-charts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk chart
        const categoryData = {
            labels: {!! json_encode($perCategory->pluck('name')) !!},
            data: {!! json_encode($perCategory->pluck('programs_count')) !!}
        };
        
        const cityData = {
            labels: {!! json_encode($perCity->pluck('city')) !!},
            data: {!! json_encode($perCity->pluck('total')) !!}
        };
        
        // Inisialisasi chart laporan
        initReportCharts(categoryData, cityData);
    });
</script>
@endpush