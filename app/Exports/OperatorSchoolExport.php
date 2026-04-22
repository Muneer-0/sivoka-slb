<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class OperatorSchoolExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $school;
    protected $programs;

    public function __construct($school)
    {
        $this->school = $school;
        $this->programs = $school->programs()->with('category')->get();
    }

    public function collection()
    {
        // Kembalikan data sekolah sebagai collection
        return collect([$this->school]);
    }

    public function headings(): array
    {
        return [
            'INFORMASI SEKOLAH',
            'DATA SEKOLAH LUAR BIASA (SLB)',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
    }

    public function map($school): array
    {
        $programCount = $this->programs->count();
        $totalStudents = $this->programs->sum('student_count');
        
        // Data sekolah dalam format baris
        $data = [
            ['NPSN', $school->npsn],
            ['Nama SLB', $school->name],
            ['Alamat', $school->address],
            ['Desa/Kelurahan', $school->village ?? '-'],
            ['Kecamatan', $school->district],
            ['Kota/Kabupaten', $school->city],
            ['Provinsi', $school->province],
            ['Telepon', $school->phone ?? '-'],
            ['Email', $school->email ?? '-'],
            ['Kepala Sekolah', $school->headmaster ?? '-'],
            ['Status', $school->status == 'negeri' ? 'Negeri' : 'Swasta'],
            ['Akreditasi', $school->accreditation ?? '-'],
            ['Latitude', $school->latitude ?? '-'],
            ['Longitude', $school->longitude ?? '-'],
            ['', ''],
            ['STATISTIK PROGRAM VOKASI', ''],
            ['Total Program', $programCount],
            ['Total Siswa', $totalStudents],
            ['Rata-rata Siswa per Program', $programCount > 0 ? round($totalStudents / $programCount) : 0],
            ['', ''],
            ['DAFTAR PROGRAM VOKASI', ''],
        ];
        
        // Tambahkan header program
        $data[] = ['No', 'Nama Program', 'Kategori', 'Jumlah Siswa', 'Jumlah Guru', 'Fasilitas', 'Produk Unggulan', 'Prestasi', 'Status'];
        
        // Tambahkan data program
        foreach ($this->programs as $index => $program) {
            $data[] = [
                $index + 1,
                $program->program_name,
                $program->category->name,
                $program->student_count,
                $program->teacher_count ?? 0,
                $program->facilities ?? '-',
                $program->products ?? '-',
                $program->achievements ?? '-',
                $program->status == 'active' ? 'Aktif' : 'Nonaktif',
            ];
        }
        
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk judul utama
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '6C757D'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Style untuk section header (Informasi Sekolah, Statistik, Daftar Program)
        $sectionRows = [4, 19, 22];
        foreach ($sectionRows as $row) {
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D6EFD'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }
        
        // Style untuk label informasi sekolah
        for ($row = 5; $row <= 18; $row++) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FC'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);
            $sheet->getStyle("B{$row}:I{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);
        }
        
        // Style untuk header program
        $programHeaderRow = 24;
        $sheet->getStyle("A{$programHeaderRow}:I{$programHeaderRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);
        
        // Style untuk data program
        $lastRow = $sheet->getHighestRow();
        for ($row = $programHeaderRow + 1; $row <= $lastRow; $row++) {
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            
            // Alternating row colors
            if ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FC'],
                    ],
                ]);
            }
        }
        
        // Style untuk statistik (baris 20-21)
        for ($row = 20; $row <= 21; $row++) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7F1FF'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);
            $sheet->getStyle("B{$row}:I{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7F1FF'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);
        }
        
        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,  // Label
            'B' => 35,  // Nilai/Nama Program
            'C' => 20,  // Kategori
            'D' => 12,  // Siswa
            'E' => 12,  // Guru
            'F' => 30,  // Fasilitas
            'G' => 30,  // Produk
            'H' => 30,  // Prestasi
            'I' => 12,  // Status
        ];
    }

    public function title(): string
    {
        return 'Data Sekolah ' . $this->school->npsn;
    }
}