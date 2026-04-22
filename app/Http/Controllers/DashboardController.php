<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Program;
use App\Models\ProgramCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isPimpinan()) {
            return $this->pimpinanDashboard();
        } else {
            return $this->operatorDashboard();
        }
    }

    private function adminDashboard()
    {
        // Data untuk grafik 1: Program per kategori
        $programsPerCategory = ProgramCategory::withCount('programs')
                                ->orderBy('programs_count', 'desc')
                                ->get();
        
        // Data untuk grafik 2: Jumlah siswa per kategori
        $studentsPerCategory = ProgramCategory::with(['programs'])
                                ->get()
                                ->map(function($category) {
                                    return [
                                        'name' => $category->name,
                                        'total_students' => $category->programs->sum('student_count')
                                    ];
                                });
        
        // Data untuk grafik 3: Top 5 program vokasi terbanyak
        $topPrograms = Program::with('category')
                       ->select('category_id', DB::raw('count(*) as total'))
                       ->groupBy('category_id')
                       ->orderBy('total', 'desc')
                       ->take(5)
                       ->get()
                       ->map(function($item) {
                           return [
                               'name' => $item->category->name,
                               'total' => $item->total
                           ];
                       });
        
        // Data untuk grafik 4: Jumlah SLB yang memiliki vokasi
        $schoolsWithVocational = School::has('programs')->count();
        $schoolsWithoutVocational = School::doesntHave('programs')->count();
        
        // Data untuk monitoring
        $schoolsData = School::withCount('programs')
                       ->withSum('programs', 'student_count')
                       ->orderBy('programs_count', 'desc')
                       ->take(10)
                       ->get();
        
        $data = [
            'totalSchools' => School::count(),
            'totalPrograms' => Program::count(),
            'totalCategories' => ProgramCategory::count(),
            'totalStudents' => Program::sum('student_count'),
            'schoolsWithData' => $schoolsWithVocational,
            'pendingSchools' => $schoolsWithoutVocational,
            'recentPrograms' => Program::with(['school', 'category'])
                                ->latest()
                                ->take(5)
                                ->get(),
            'programsPerCategory' => $programsPerCategory,
            'studentsPerCategory' => $studentsPerCategory,
            'topPrograms' => $topPrograms,
            'schoolsData' => $schoolsData,
            'chartLabels' => $programsPerCategory->pluck('name'),
            'chartData' => $programsPerCategory->pluck('programs_count'),
            'studentLabels' => $studentsPerCategory->pluck('name'),
            'studentData' => $studentsPerCategory->pluck('total_students'),
        ];
        
        return view('dashboard.admin', $data);
    }

    private function pimpinanDashboard()
    {
        // Ambil data kota/kab unik dari tabel schools
        $cities = School::distinct('city')->orderBy('city')->pluck('city');
        
        // Data untuk grafik (versi ringkas untuk pimpinan)
        $programsPerCategory = ProgramCategory::withCount('programs')
                                ->orderBy('programs_count', 'desc')
                                ->take(5)
                                ->get();
        
        $studentsPerCategory = ProgramCategory::with(['programs'])
                                ->get()
                                ->map(function($category) {
                                    return [
                                        'name' => $category->name,
                                        'total_students' => $category->programs->sum('student_count')
                                    ];
                                })->take(5);
        
        $recentSchools = School::withCount('programs')
                        ->withSum('programs', 'student_count')
                        ->having('programs_count', '>', 0)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
        
        $data = [
            'totalSchools' => School::count(),
            'totalPrograms' => Program::count(),
            'totalStudents' => Program::sum('student_count'),
            'schoolsWithData' => School::has('programs')->count(),
            'pendingSchools' => School::doesntHave('programs')->count(),
            'programsPerCategory' => $programsPerCategory,
            'studentsPerCategory' => $studentsPerCategory,
            'recentSchools' => $recentSchools,
            'cities' => $cities, // TAMBAHKAN INI
        ];
        
        return view('dashboard.pimpinan', $data);
    }

    private function operatorDashboard()
    {
        $user = Auth::user();
        $school = $user->school;
        
        if (!$school) {
            return view('dashboard.operator-noschool');
        }
        
        $programs = Program::where('school_id', $school->id)
                    ->with('category')
                    ->get();
        
        $totalStudents = $programs->sum('student_count');
        $totalPrograms = $programs->count();
        
        $statsPerCategory = ProgramCategory::withCount(['programs' => function($q) use ($school) {
                                $q->where('school_id', $school->id);
                            }])->get();
        
        $data = [
            'school' => $school,
            'programs' => $programs,
            'totalPrograms' => $totalPrograms,
            'totalStudents' => $totalStudents,
            'statsPerCategory' => $statsPerCategory,
            'categories' => ProgramCategory::all()
        ];
        
        return view('dashboard.operator', $data);
    }
}