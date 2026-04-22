<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SchoolsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = School::withCount('programs');
        
        if (!empty($this->filters['city'])) {
            $query->where('city', $this->filters['city']);
        }
        
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (!empty($this->filters['search'])) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('npsn', 'like', '%' . $this->filters['search'] . '%');
            });
        }
        
        return $query->orderBy('city')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NPSN',
            'Nama SLB',
            'Alamat',
            'Desa/Kelurahan',
            'Kecamatan',
            'Kota/Kabupaten',
            'Provinsi',
            'Telepon',
            'Email',
            'Kepala Sekolah',
            'Status',
            'Akreditasi',
            'Latitude',
            'Longitude',
            'Jumlah Program',
            'Tanggal Dibuat'
        ];
    }

    public function map($school): array
    {
        static $row = 0;
        $row++;
        
        return [
            $row,
            $school->npsn,
            $school->name,
            $school->address,
            $school->village ?? '-',
            $school->district,
            $school->city,
            $school->province,
            $school->phone ?? '-',
            $school->email ?? '-',
            $school->headmaster ?? '-',
            $school->status == 'negeri' ? 'Negeri' : 'Swasta',
            $school->accreditation ?? '-',
            $school->latitude ?? '-',
            $school->longitude ?? '-',
            $school->programs_count,
            $school->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Q1')->applyFromArray([
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

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:Q' . $lastRow)->applyFromArray([
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

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, 'B' => 12, 'C' => 35, 'D' => 40, 'E' => 15,
            'F' => 20, 'G' => 20, 'H' => 15, 'I' => 15, 'J' => 25,
            'K' => 25, 'L' => 12, 'M' => 10, 'N' => 15, 'O' => 15,
            'P' => 12, 'Q' => 18,
        ];
    }
}