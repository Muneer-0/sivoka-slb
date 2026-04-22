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
        $data = [
            'totalSchools' => School::count(),
            'totalPrograms' => Program::count(),
            'totalCategories' => ProgramCategory::count(),
            'totalStudents' => Program::sum('student_count'),
            'schoolsWithData' => School::has('programs')->count(),
            'pendingSchools' => School::doesntHave('programs')->count(),
            'recentPrograms' => Program::with(['school', 'category'])
                                ->latest()
                                ->take(5)
                                ->get(),
            'programsPerCategory' => ProgramCategory::withCount('programs')
                                    ->get(),
        ];
        
        return view('dashboard.admin', $data);
    }

    private function pimpinanDashboard()
    {
        $data = [
            'totalSchools' => School::count(),
            'totalPrograms' => Program::count(),
            'totalStudents' => Program::sum('student_count'),
            'schoolsWithData' => School::has('programs')->count(),
            'recentSchools' => School::withCount('programs')
                              ->having('programs_count', '>', 0)
                              ->orderBy('created_at', 'desc')
                              ->take(5)
                              ->get()
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
        
        $data = [
            'school' => $school,
            'programs' => Program::where('school_id', $school->id)
                         ->with('category')
                         ->get(),
            'totalPrograms' => Program::where('school_id', $school->id)->count(),
            'totalStudents' => Program::where('school_id', $school->id)->sum('student_count'),
            'categories' => ProgramCategory::all()
        ];
        
        return view('dashboard.operator', $data);
    }
}