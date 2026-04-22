<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\School;
use App\Models\ProgramCategory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ProgramsExport;
use App\Exports\SchoolsExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Program::class);
        
        $data = $this->getReportData($request);
        
        return view('reports.index', $data);
    }

    private function getReportData($request)
    {
        $query = Program::with(['school', 'category']);
        
        // Apply filters
        if ($request->filled('city')) {
            $query->whereHas('school', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $programs = $query->get();
        
        $stats = [
            'totalSchools' => $programs->groupBy('school_id')->count(),
            'totalPrograms' => $programs->count(),
            'totalStudents' => $programs->sum('student_count'),
            'totalCategories' => $programs->groupBy('category_id')->count(),
        ];
        
        $perCategory = ProgramCategory::withCount('programs')
                        ->orderBy('programs_count', 'desc')
                        ->get();
        
        $perCity = Program::select('schools.city', DB::raw('count(*) as total'))
                   ->join('schools', 'programs.school_id', '=', 'schools.id')
                   ->groupBy('schools.city')
                   ->orderBy('total', 'desc')
                   ->get();
        
        $topSchools = School::withCount('programs')
                      ->withSum('programs', 'student_count')
                      ->having('programs_count', '>', 0)
                      ->orderBy('programs_count', 'desc')
                      ->take(10)
                      ->get();
        
        return [
            'programs' => $programs,
            'stats' => $stats,
            'perCategory' => $perCategory,
            'perCity' => $perCity,
            'topSchools' => $topSchools,
            'filters' => [
                'cities' => School::distinct('city')->orderBy('city')->pluck('city'),
                'categories' => ProgramCategory::all(),
                'schools' => School::orderBy('name')->get(),
                'selectedCity' => $request->city,
                'selectedCategory' => $request->category,
                'selectedSchool' => $request->school_id,
                'dateFrom' => $request->date_from,
                'dateTo' => $request->date_to,
            ]
        ];
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('viewAny', Program::class);
        
        $filters = [
            'city' => $request->city,
            'category' => $request->category,
            'school_id' => $request->school_id,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];
        
        // Jika type = schools, export data sekolah
        if ($request->type == 'schools') {
            $schoolFilters = [
                'city' => $request->city,
                'status' => $request->status,
                'search' => $request->search,
            ];
            return Excel::download(new SchoolsExport($schoolFilters), 'laporan-slb.xlsx');
        }
        
        return Excel::download(new ProgramsExport($filters), 'laporan-program-vokasi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('viewAny', Program::class);
        
        $data = $this->getReportData($request);
        
        $pdf = Pdf::loadView('reports.pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('laporan-program-vokasi.pdf');
    }
}