<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Program Vokasi - {{ $school->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #0d6efd;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #666;
        }
        .school-info {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        .school-info td {
            padding: 3px 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #0d6efd;
            color: white;
            padding: 8px;
            font-size: 10px;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
        }
        .summary {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: #f8f9fc;
            padding: 10px;
            text-align: center;
            flex: 1;
            border-top: 3px solid #0d6efd;
        }
        .summary-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #0d6efd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SiVOKA-SLB</h1>
        <h2>Sistem Informasi Vokasi SLB</h2>
        <h3>LAPORAN PROGRAM VOKASI</h3>
        <p>{{ $school->name }}</p>
        <p>Periode: {{ date('d F Y') }}</p>
    </div>
    
    <div class="school-info">
         <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; width: 120px;"><strong>NPSN</strong></td>
                <td style="border: none;">: {{ $school->npsn }}</td>
                <td style="border: none; width: 120px;"><strong>Akreditasi</strong></td>
                <td style="border: none;">: {{ $school->accreditation ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Alamat</strong></td>
                <td style="border: none;" colspan="3">: {{ $school->address }}, {{ $school->city }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Kepala Sekolah</strong></td>
                <td style="border: none;" colspan="3">: {{ $school->headmaster ?? '-' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="summary">
        <div class="summary-card">
            <div>Total Program</div>
            <div class="value">{{ $totalPrograms }}</div>
        </div>
        <div class="summary-card">
            <div>Total Siswa</div>
            <div class="value">{{ $totalStudents }}</div>
        </div>
        <div class="summary-card">
            <div>Rata-rata/Program</div>
            <div class="value">{{ $totalPrograms > 0 ? round($totalStudents / $totalPrograms) : 0 }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Program</th>
                <th>Kategori</th>
                <th>Siswa</th>
                <th>Guru</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programs as $index => $program)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $program->program_name }}</td>
                <td>{{ $program->category->name }}</td>
                <td style="text-align: center;">{{ $program->student_count }}</td>
                <td style="text-align: center;">{{ $program->teacher_count ?? 0 }}</td>
                <td style="text-align: center;">{{ $program->status == 'active' ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="signature">
        <p>Medan, {{ date('d F Y') }}</p>
        <p>Operator {{ $school->name }}</p>
        <br><br><br>
        <p><strong>{{ auth()->user()->name }}</strong></p>
    </div>
    
    <div class="footer">
        <p>Dicetak dari SiVOKA-SLB pada {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>