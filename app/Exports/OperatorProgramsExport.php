<?php

namespace App\Exports;

use App\Models\Program;
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

class OperatorProgramsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $school;
    protected $schoolName;

    public function __construct($school)
    {
        $this->school = $school;
        $this->schoolName = $school->name;
    }

    public function collection()
    {
        return Program::where('school_id', $this->school->id)
                    ->with('category')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Program',
            'Kategori',
            'Jumlah Siswa',
            'Jumlah Guru',
            'Fasilitas',
            'Produk Unggulan',
            'Prestasi',
            'Status',
            'Tanggal Dibuat'
        ];
    }

    public function map($program): array
    {
        static $row = 0;
        $row++;
        
        return [
            $row,
            $program->program_name,
            $program->category->name,
            $program->student_count,
            $program->teacher_count ?? 0,
            $program->facilities ?? '-',
            $program->products ?? '-',
            $program->achievements ?? '-',
            $program->status == 'active' ? 'Aktif' : 'Nonaktif',
            $program->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
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
        
        // Style untuk data
        $lastRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
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
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FC'],
                    ],
                ]);
            }
        }
        
        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 35,  // Nama Program
            'C' => 20,  // Kategori
            'D' => 12,  // Siswa
            'E' => 12,  // Guru
            'F' => 35,  // Fasilitas
            'G' => 35,  // Produk
            'H' => 35,  // Prestasi
            'I' => 10,  // Status
            'J' => 18,  // Tanggal
        ];
    }

    public function title(): string
    {
        return 'Program Vokasi';
    }
}