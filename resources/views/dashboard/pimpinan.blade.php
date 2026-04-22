@extends('layouts.dashboard')

@section('title', 'Dashboard Pimpinan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sivoka-pimpinan-dashboard.css') }}">
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
            {{ $totalSchools }} SLB
        </div>
        <div class="quick-stat">
            <i class="bi bi-bar-chart"></i>
            {{ $totalPrograms }} Program Vokasi
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
                <i class="bi bi-grid"></i> {{ $schoolsWithData }} SLB aktif
            </div>
        </div>
    </div>
    
    <div class="stat-card program">
        <i class="bi bi-bar-chart stat-icon"></i>
        <div class="stat-content">
            <div class="stat-label">PROGRAM VOKASI</div>
            <div class="stat-value">{{ $totalPrograms }}</div>
            <div class="stat-footer">
                <i class="bi bi-tags"></i> {{ $programsPerCategory->count() ?? 0 }} kategori
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
            Progress Pengisian Data Program Vokasi
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
    <!-- Top 5 Program Vokasi -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-bar-chart"></i>
            <h6>5 Program Vokasi Teratas</h6>
        </div>
        <div class="chart-container">
            <canvas id="topProgramsChart"></canvas>
        </div>
        <div class="chart-footer">
            <span>Total Program: {{ $totalPrograms }}</span>
            <span>5 Teratas: {{ $programsPerCategory->sum('programs_count') }}</span>
        </div>
    </div>
    
    <!-- Sebaran Siswa per Kategori -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="bi bi-people"></i>
            <h6>Sebaran Siswa per Kategori</h6>
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

<!-- 5 SLB dengan Program Vokasi Terbaru -->
<div class="table-card">
    <div class="table-header">
        <h5>
            <i class="bi bi-clock-history"></i>
            5 SLB dengan Program Vokasi Terbaru
        </h5>
        <a href="{{ route('programs.index') }}">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama SLB</th>
                    <th>Kota/Kab</th>
                    <th class="text-center">Program</th>
                    <th class="text-center">Siswa</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSchools as $index => $school)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="school-name-cell">{{ $school->name }}</div>
                        <small class="text-muted">NPSN: {{ $school->npsn }}</small>
                    </td>
                    <td>{{ $school->city }}</td>
                    <td class="text-center">
                        <span class="program-badge">
                            <i class="bi bi-bar-chart"></i> {{ $school->programs_count }}
                        </span>
                    </td>
                    <td class="text-center">{{ number_format($school->programs_sum_student_count ?? 0) }}</td>
                    <td class="text-center">
                        <a href="{{ route('schools.show', $school) }}" class="btn-detail">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada data program vokasi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sivoka-pimpinan-dashboard.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Data untuk chart
            const chartData = {
                topLabels: {!! json_encode($programsPerCategory->pluck('name')->take(5)) !!},
                topData: {!! json_encode($programsPerCategory->pluck('programs_count')->take(5)) !!},
                studentLabels: {!! json_encode($studentsPerCategory->pluck('name')->take(5)) !!},
                studentData: {!! json_encode($studentsPerCategory->pluck('total_students')->take(5)) !!}
            };
            
            console.log('Chart data loaded:', chartData);
            
            // Inisialisasi chart
            const charts = initPimpinanDashboard(chartData);
            
            if (charts.topProgramsChart || charts.studentsChart) {
                console.log('Charts initialized successfully');
            } else {
                console.warn('No charts were initialized');
            }
        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    });
</script>
@endpush