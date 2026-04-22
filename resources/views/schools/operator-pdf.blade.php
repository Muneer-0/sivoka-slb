<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Sekolah - {{ $school->name }}</title>
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
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #0d6efd;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .header h3 {
            font-size: 16px;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-section .label {
            width: 140px;
            font-weight: bold;
            background: #f8f9fc;
        }
        .summary-section {
            margin-bottom: 25px;
        }
        .summary-cards {
            display: flex;
            gap: 15px;
        }
        .summary-card {
            flex: 1;
            background: #f8f9fc;
            padding: 12px;
            text-align: center;
            border-top: 3px solid #0d6efd;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }
        .program-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .program-section th {
            background: #28a745;
            color: white;
            padding: 8px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #ddd;
        }
        .program-section td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
        }
        .program-section .text-center {
            text-align: center;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>PEMERINTAH PROVINSI SUMATERA UTARA</h1>
        <h2>DINAS PENDIDIKAN</h2>
        <h3>DATA SEKOLAH LUAR BIASA (SLB)</h3>
        <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
    </div>
    
    <div class="info-section">
         <table style="width: 100%;">
            <tr style="background: #0d6efd; color: white;">
                <td colspan="2" style="padding: 10px; font-weight: bold; font-size: 12px;">
                    <i class="bi bi-building"></i> INFORMASI SEKOLAH
                </td>
            </tr>
            <tr><td class="label">NPSN</td><td>{{ $school->npsn }}</td></tr>
            <tr><td class="label">Nama SLB</td><td>{{ $school->name }}</td></tr>
            <tr><td class="label">Alamat</td><td>{{ $school->address }}</td></tr>
            <tr><td class="label">Desa/Kelurahan</td><td>{{ $school->village ?? '-' }}</td></tr>
            <tr><td class="label">Kecamatan</td><td>{{ $school->district }}</td></tr>
            <tr><td class="label">Kota/Kabupaten</td><td>{{ $school->city }}</td></tr>
            <tr><td class="label">Provinsi</td><td>{{ $school->province }}</td></tr>
            <tr><td class="label">Telepon</td><td>{{ $school->phone ?? '-' }}</td></tr>
            <tr><td class="label">Email</td><td>{{ $school->email ?? '-' }}</td></tr>
            <tr><td class="label">Kepala Sekolah</td><td>{{ $school->headmaster ?? '-' }}</td></tr>
            <tr><td class="label">Status</td><td>{{ $school->status == 'negeri' ? 'Negeri' : 'Swasta' }}</td></tr>
            <tr><td class="label">Akreditasi</td><td>{{ $school->accreditation ?? '-' }}</td></tr>
            <tr><td class="label">Latitude</td><td>{{ $school->latitude ?? '-' }}</td></tr>
            <tr><td class="label">Longitude</td><td>{{ $school->longitude ?? '-' }}</td></tr>
        </table>
    </div>
    
    <div class="summary-section">
        <div class="summary-cards">
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
    </div>
    
    <div class="program-section">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Program</th>
                    <th>Kategori</th>
                    <th>Jumlah Siswa</th>
                    <th>Jumlah Guru</th>
                    <th>Fasilitas</th>
                    <th>Produk Unggulan</th>
                    <th>Prestasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $index => $program)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $program->program_name }}</td>
                    <td>{{ $program->category->name }}</td>
                    <td class="text-center">{{ $program->student_count }}</td>
                    <td class="text-center">{{ $program->teacher_count ?? 0 }}</td>
                    <td>{{ $program->facilities ?? '-' }}</td>
                    <td>{{ $program->products ?? '-' }}</td>
                    <td>{{ $program->achievements ?? '-' }}</td>
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
    </div>
    
    <div class="signature">
        <p>Medan, {{ date('d F Y') }}</p>
        <p>Operator {{ $school->name }}</p>
        <br><br><br>
        <p><strong>{{ auth()->user()->name }}</strong></p>
        <p>NIP. {{ auth()->user()->nip ?? '____________________' }}</p>
    </div>
    
    <div class="footer">
        <p>Dokumen ini dicetak dari Sistem Informasi Vokasi SLB (SiVOKA-SLB)</p>
        <p>Dicetak pada: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>