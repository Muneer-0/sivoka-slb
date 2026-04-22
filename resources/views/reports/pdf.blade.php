<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Program Vokasi SLB</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0d6efd;
        }
        
        .header h1 {
            color: #0d6efd;
            font-size: 22px;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
            margin-bottom: 8px;
        }
        
        .header h3 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .header p {
            font-size: 10px;
            color: #888;
            margin-top: 5px;
        }
        
        /* Filter Info */
        .filter-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        
        .filter-info table {
            width: 100%;
        }
        
        .filter-info td {
            padding: 3px 5px;
            font-size: 10px;
        }
        
        .filter-info .label {
            font-weight: bold;
            width: 80px;
        }
        
        /* Summary Cards */
        .summary {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .summary-card {
            flex: 1;
            min-width: 120px;
            background: #f8f9fc;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            border-top: 3px solid #0d6efd;
        }
        
        .summary-card .title {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-card .value {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .summary-card .desc {
            font-size: 9px;
            color: #888;
            margin-top: 3px;
        }
        
        /* Section Title */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0d6efd;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background: #0d6efd;
            color: white;
            padding: 8px 6px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 9px;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #ddd;
        }
        
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        
        .signature p {
            margin: 5px 0;
        }
        
        /* Page Break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PEMERINTAH PROVINSI SUMATERA UTARA</h1>
        <h2>DINAS PENDIDIKAN</h2>
        <h3>LAPORAN PROGRAM VOKASI SEKOLAH LUAR BIASA (SLB)</h3>
        <p>Periode: {{ date('d F Y') }}</p>
    </div>
    
    <!-- Filter Info -->
    <div class="filter-info">
        <table>
             <tr>
                <td class="label">Periode Laporan</td>
                <td>: {{ date('d F Y') }}</td>
                @if(!empty($filters['selectedCity']))
                <td class="label">Kota/Kab</td>
                <td>: {{ $filters['selectedCity'] }}</td>
                @endif
             </tr>
             <tr>
                @if(!empty($filters['selectedCategory']))
                <td class="label">Kategori</td>
                <td>: {{ $filters['categories']->firstWhere('id', $filters['selectedCategory'])->name ?? 'Semua' }}</td>
                @endif
                @if(!empty($filters['selectedSchool']))
                <td class="label">SLB</td>
                <td>: {{ $filters['schools']->firstWhere('id', $filters['selectedSchool'])->name ?? 'Semua' }}</td>
                @endif
             </tr>
             <tr>
                @if(!empty($filters['dateFrom']))
                <td class="label">Tanggal Mulai</td>
                <td>: {{ \Carbon\Carbon::parse($filters['dateFrom'])->format('d/m/Y') }}</td>
                @endif
                @if(!empty($filters['dateTo']))
                <td class="label">Tanggal Akhir</td>
                <td>: {{ \Carbon\Carbon::parse($filters['dateTo'])->format('d/m/Y') }}</td>
                @endif
             </tr>
        </table>
    </div>
    
    <!-- Summary Cards -->
    <div class="summary">
        <div class="summary-card">
            <div class="title">Total SLB</div>
            <div class="value">{{ $stats['totalSchools'] ?? 0 }}</div>
            <div class="desc">SLB dengan program vokasi</div>
        </div>
        <div class="summary-card">
            <div class="title">Total Program</div>
            <div class="value">{{ $stats['totalPrograms'] ?? 0 }}</div>
            <div class="desc">Program vokasi aktif</div>
        </div>
        <div class="summary-card">
            <div class="title">Total Siswa</div>
            <div class="value">{{ number_format($stats['totalStudents'] ?? 0) }}</div>
            <div class="desc">Peserta program vokasi</div>
        </div>
        <div class="summary-card">
            <div class="title">Total Kategori</div>
            <div class="value">{{ $stats['totalCategories'] ?? 0 }}</div>
            <div class="desc">Jenis keterampilan</div>
        </div>
    </div>
    
    <!-- Program per Kategori -->
    <div class="section-title">
        📊 PROGRAM PER KATEGORI
    </div>
    <table>
        <thead>
             <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Jumlah Program</th>
                <th>Persentase</th>
             </tr>
        </thead>
        <tbody>
            @php $total = $perCategory->sum('programs_count'); @endphp
            @foreach($perCategory as $index => $item)
             <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->programs_count }}</td>
                <td class="text-center">{{ $total > 0 ? round(($item->programs_count / $total) * 100, 1) : 0 }}%</td>
             </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Program per Kota -->
    <div class="section-title">
        🏙️ PROGRAM PER KOTA/KABUPATEN
    </div>
    <table>
        <thead>
             <tr>
                <th>No</th>
                <th>Kota/Kabupaten</th>
                <th>Jumlah Program</th>
                <th>Persentase</th>
             </tr>
        </thead>
        <tbody>
            @php $total = $perCity->sum('total'); @endphp
            @foreach($perCity as $index => $item)
             <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->city }}</td>
                <td class="text-center">{{ $item->total }}</td>
                <td class="text-center">{{ $total > 0 ? round(($item->total / $total) * 100, 1) : 0 }}%</td>
             </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- 10 SLB Terbaik -->
    <div class="section-title">
        🏆 10 SLB DENGAN PROGRAM VOKASI TERBANYAK
    </div>
    <table>
        <thead>
             <tr>
                <th>No</th>
                <th>Nama SLB</th>
                <th>Kota/Kab</th>
                <th>Jumlah Program</th>
                <th>Total Siswa</th>
                <th>Akreditasi</th>
             </tr>
        </thead>
        <tbody>
            @foreach($topSchools as $index => $school)
             <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $school->name }}</td>
                <td>{{ $school->city }}</td>
                <td class="text-center">{{ $school->programs_count }}</td>
                <td class="text-center">{{ number_format($school->programs_sum_student_count ?? 0) }}</td>
                <td class="text-center">
                    @if($school->accreditation)
                        <span class="badge badge-success">{{ $school->accreditation }}</span>
                    @else
                        -
                    @endif
                </td>
             </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Detail Program -->
    <div class="section-title">
        📋 DETAIL PROGRAM VOKASI
    </div>
    <table>
        <thead>
             <tr>
                <th>No</th>
                <th>SLB</th>
                <th>Kota</th>
                <th>Kategori</th>
                <th>Program</th>
                <th>Siswa</th>
                <th>Status</th>
             </tr>
        </thead>
        <tbody>
            @foreach($programs as $index => $program)
             <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $program->school->name }}</td>
                <td>{{ $program->school->city }}</td>
                <td>{{ $program->category->name }}</td>
                <td>{{ $program->program_name }}</td>
                <td class="text-center">{{ $program->student_count }}</td>
                <td class="text-center">
                    @if($program->status == 'active')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-warning">Nonaktif</span>
                    @endif
                </td>
             </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Informasi Vokasi SLB (SiVOKA-SLB)</p>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</p>
    </div>
    
    <!-- Signature -->
    <div class="signature">
        <p>Medan, {{ date('d F Y') }}</p>
        <p>Kepala Bidang Pembinaan Pendidikan Khusus</p>
        <br><br><br>
        <p><strong>{{ auth()->user()->name ?? '____________________' }}</strong></p>
        <p>NIP. ____________________</p>
    </div>
</body>
</html>