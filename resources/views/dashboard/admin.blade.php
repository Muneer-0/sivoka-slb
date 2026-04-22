@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-dashboard.css') }}">
@endpush

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <h4 class="welcome-title">Selamat Datang, {{ auth()->user()->name }}! 👋</h4>
    <div class="welcome-date">
        <i class="bi bi-calendar"></i>
        {{ date('d F Y') }}
    </div>
    
    <div class="quick-stats">
        <div class="quick-stat">
            <i class="bi bi-building"></i>
            {{ $totalSchools }} SLB • 33 kab/kota
        </div>
        <div class="quick-stat">
            <i class="bi bi-tags"></i>
            {{ $totalCategories }} Kategori Vokasi
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card total-slb">
        <i class="bi bi-building stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">TOTAL SLB</div>
            <div class="stat-value">{{ $totalSchools }}</div>
            <div class="stat-footer">
                <i class="bi bi-grid"></i> 33 kab/kota
            </div>
        </div>
    </div>
    
    <div class="stat-card program">
        <i class="bi bi-bar-chart stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">PROGRAM VOKASI</div>
            <div class="stat-value">{{ $totalPrograms }}</div>
            <div class="stat-footer">
                <i class="bi bi-tags"></i> {{ $totalCategories }} kategori
            </div>
        </div>
    </div>
    
    <div class="stat-card siswa">
        <i class="bi bi-people stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">TOTAL SISWA</div>
            <div class="stat-value">{{ number_format($totalStudents) }}</div>
            <div class="stat-footer">
                <i class="bi bi-person-check"></i> mengikuti vokasi
            </div>
        </div>
    </div>
    
    <div class="stat-card aktif">
        <i class="bi bi-check-circle stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">SLB AKTIF</div>
            <div class="stat-value">{{ $schoolsWithData }}/{{ $totalSchools }}</div>
            <div class="stat-footer">
                <i class="bi bi-graph-up"></i> {{ round(($schoolsWithData/$totalSchools)*100) }}% input data
            </div>
        </div>
    </div>
</div>

<!-- Progress Card -->
@php $percentage = $totalSchools > 0 ? round(($schoolsWithData/$totalSchools)*100) : 0; @endphp
<div class="progress-card">
    <div class="progress-header">
        <h5>
            <i class="bi bi-pie-chart"></i>
            Progress Pengisian Data
        </h5>
        <span class="progress-percentage">{{ $percentage }}%</span>
    </div>
    
    <div class="progress-bar-container">
        <div class="progress-bar-fill" style="width: {{ $percentage }}%;"></div>
    </div>
    
    <div class="progress-stats">
        <span><i class="bi bi-check-circle-fill text-success"></i> {{ $schoolsWithData }} SLB sudah input</span>
        <span><i class="bi bi-exclamation-triangle-fill text-warning"></i> {{ $pendingSchools }} SLB belum input</span>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-grid">
    <!-- Program per Kategori -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-bar-chart"></i>
            <h6>Program per Kategori</h6>
        </div>
        <div class="chart-container">
            <canvas id="programsChart"></canvas>
        </div>
        <div class="chart-footer">
            <span>Total Kategori: {{ $programsPerCategory->count() }}</span>
            <span>Total Program: {{ $totalPrograms }}</span>
        </div>
    </div>
    
    <!-- Siswa per Kategori -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-people"></i>
            <h6>Siswa per Kategori</h6>
        </div>
        <div class="chart-container">
            <canvas id="studentsChart"></canvas>
        </div>
        <div class="chart-footer">
            <span>Total Siswa: {{ number_format($totalStudents) }}</span>
            <span>Rata-rata: {{ $totalPrograms > 0 ? round($totalStudents/$totalPrograms) : 0 }}/program</span>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="tables-grid">
    <!-- Top 5 SLB -->
    <div class="table-card">
        <div class="table-header">
            <h6>
                <i class="bi bi-trophy"></i>
                Top 5 SLB dengan Program Terbanyak
            </h6>
            <a href="{{ route('monitoring.index') }}">Lihat Semua <i class="bi bi-arrow-right"></i></a>
        </div>
        
        <table class="table-simple">
            @foreach($schoolsData->take(5) as $school)
            <tr>
                <td class="school-name-simple">{{ $school->name }}</td>
                <td class="school-value-simple">{{ $school->programs_count }} program</td>
            </tr>
            @endforeach
        </table>
    </div>
    
    <!-- Program Terbaru -->
    <div class="table-card">
        <div class="table-header">
            <h6>
                <i class="bi bi-clock-history"></i>
                Program Vokasi Terbaru
            </h6>
            <a href="{{ route('programs.index') }}">Lihat Semua <i class="bi bi-arrow-right"></i></a>
        </div>
        
        @foreach($recentPrograms->take(5) as $program)
        <div class="program-item">
            <span class="name">{{ $program->program_name }}</span>
            <span class="value">{{ $program->school->name }}</span>
        </div>
        @endforeach
    </div>
</div>

<!-- Top Programs Chart (baris kedua) -->
<div class="charts-grid">
    <!-- Top 5 Program Vokasi -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-trophy"></i>
            <h6>Top 5 Program Vokasi</h6>
        </div>
        <div class="chart-container">
            <canvas id="topProgramsChart"></canvas>
        </div>
    </div>
    
    <!-- Progress Chart (doughnut) -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-pie-chart"></i>
            <h6>Status Pengisian Data</h6>
        </div>
        <div class="chart-container">
            <canvas id="progressChart"></canvas>
        </div>
        <div class="chart-footer">
            <span><i class="bi bi-check-circle text-success"></i> {{ $schoolsWithData }} Sudah</span>
            <span><i class="bi bi-exclamation-triangle text-warning"></i> {{ $pendingSchools }} Belum</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-charts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk chart
        const chartData = {
            programLabels: {!! json_encode($programsPerCategory->pluck('name')) !!},
            programData: {!! json_encode($programsPerCategory->pluck('programs_count')) !!},
            studentLabels: {!! json_encode($studentsPerCategory->pluck('name')) !!},
            studentData: {!! json_encode($studentsPerCategory->pluck('total_students')) !!},
            topLabels: {!! json_encode($topPrograms->pluck('name')) !!},
            topData: {!! json_encode($topPrograms->pluck('total')) !!},
            schoolsWithData: {{ $schoolsWithData }},
            pendingSchools: {{ $pendingSchools }}
        };
        
        // Inisialisasi chart
        const charts = initDashboardCharts(chartData);
        
        // Setup resize handler
        setupChartResize(charts);
    });
</script>
@endpush