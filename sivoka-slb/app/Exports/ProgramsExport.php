<?php

namespace App\Exports;

use App\Models\Program;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProgramsExport implements FromCollection, WithHeadings, WithMapping
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
            'Nama SLB',
            'Kota/Kab',
            'Kategori',
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
            $program->school->name,
            $program->school->city,
            $program->category->name,
            $program->program_name,
            $program->student_count,
            $program->teacher_count ?? 0,
            $program->facilities ?? '-',
            $program->products ?? '-',
            $program->achievements ?? '-',
            $program->status == 'active' ? 'Aktif' : 'Nonaktif',
            $program->created_at->format('d/m/Y'),
        ];
    }
}