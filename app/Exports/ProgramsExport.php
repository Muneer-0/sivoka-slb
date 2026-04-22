<?php

namespace App\Exports;

use App\Models\Program;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ProgramsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Program::with(['school', 'category']);
        
        if (!empty($this->filters['city'])) {
            $query->whereHas('school', function($q) {
                $q->where('city', $this->filters['city']);
            });
        }
        
        if (!empty($this->filters['category'])) {
            $query->where('category_id', $this->filters['category']);
        }
        
        if (!empty($this->filters['school_id'])) {
            $query->where('school_id', $this->filters['school_id']);
        }
        
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NPSN',
            'Nama SLB',
            'Kota/Kabupaten',
            'Kecamatan',
            'Kategori Program',
            'Nama Program',
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
            $program->school->npsn,
            $program->school->name,
            $program->school->city,
            $program->school->district,
            $program->category->name,
            $program->program_name,
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
        // Style untuk header
        $sheet->getStyle('A1:N1')->applyFromArray([
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

        // Style untuk semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:N' . $lastRow)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);

        // Auto size untuk semua kolom
        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 12,  // NPSN
            'C' => 35,  // Nama SLB
            'D' => 20,  // Kota/Kab
            'E' => 20,  // Kecamatan
            'F' => 20,  // Kategori
            'G' => 30,  // Nama Program
            'H' => 12,  // Siswa
            'I' => 12,  // Guru
            'J' => 30,  // Fasilitas
            'K' => 30,  // Produk
            'L' => 30,  // Prestasi
            'M' => 10,  // Status
            'N' => 18,  // Tanggal
        ];
    }
}