<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Program;
use App\Models\ProgramCategory;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', School::class);
        
        $query = School::withCount(['programs'])
                 ->withSum('programs', 'student_count');
        
        // Filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('program_status')) {
            if ($request->program_status == 'lengkap') {
                $query->has('programs', '>', 0);
            } elseif ($request->program_status == 'belum') {
                $query->doesntHave('programs');
            }
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('npsn', 'like', '%' . $request->search . '%');
            });
        }
        
        $schools = $query->orderBy('programs_count', 'desc')->paginate(20);
        
        $cities = School::distinct('city')->orderBy('city')->pluck('city');
        $statuses = ['negeri' => 'Negeri', 'swasta' => 'Swasta'];
        
        // Statistik monitoring
        $totalSchools = School::count();
        $schoolsWithData = School::has('programs')->count();
        $schoolsWithoutData = School::doesntHave('programs')->count();
        $totalPrograms = Program::count();
        $totalStudents = Program::sum('student_count');
        
        return view('monitoring.index', compact(
            'schools', 'cities', 'statuses',
            'totalSchools', 'schoolsWithData', 'schoolsWithoutData',
            'totalPrograms', 'totalStudents'
        ));
    }
}