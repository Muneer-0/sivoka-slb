<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\School;
use App\Models\ProgramCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OperatorProgramsExport;
use App\Exports\OperatorSchoolExport;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Program::class);
        
        $query = Program::with(['school', 'category']);
        
        // Filter
        if ($request->filled('city')) {
            $query->whereHas('school', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('search')) {
            $query->where('program_name', 'like', '%' . $request->search . '%');
        }
        
        $programs = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $cities = School::distinct('city')->orderBy('city')->pluck('city');
        $categories = ProgramCategory::global()->orderBy('name')->get(); // Hanya kategori global untuk admin
        
        return view('programs.index', compact('programs', 'cities', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Program::class);
        
        $user = Auth::user();
        
        if ($user->isOperator()) {
            if (!$user->school) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda belum terdaftar sebagai operator SLB.');
            }
            $schools = collect([$user->school]);
            
            // Untuk operator: ambil kategori GLOBAL + kategori LOKAL sekolahnya
            $globalCategories = ProgramCategory::global()->orderBy('name')->get();
            $localCategories = ProgramCategory::local($user->school_id)->orderBy('name')->get();
            $categories = $globalCategories->merge($localCategories);
        } else {
            $schools = School::orderBy('name')->get();
            // Untuk admin: hanya kategori global
            $categories = ProgramCategory::global()->orderBy('name')->get();
        }
        
        return view('programs.create', compact('schools', 'categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Program::class);
        
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'category_id' => 'required|exists:program_categories,id',
            'program_name' => 'required|max:255',
            'description' => 'nullable',
            'student_count' => 'required|integer|min:0',
            'teacher_count' => 'nullable|integer|min:0',
            'facilities' => 'nullable',
            'products' => 'nullable',
            'achievements' => 'nullable',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'active';
        
        $user = Auth::user();
        
        // Validasi operator hanya untuk sekolahnya sendiri
        if ($user->isOperator() && $user->school_id != $request->school_id) {
            return redirect()->back()
                ->with('error', 'Anda hanya dapat menambah data untuk sekolah Anda sendiri.');
        }
        
        // Validasi: operator tidak bisa menggunakan kategori lokal milik sekolah lain
        if ($user->isOperator()) {
            $category = ProgramCategory::find($request->category_id);
            if (!$category->is_global && $category->school_id != $user->school_id) {
                return redirect()->back()
                    ->with('error', 'Anda tidak dapat menggunakan kategori dari sekolah lain.');
            }
        }

        Program::create($validated);

        // Redirect sesuai role
        if ($user->isOperator()) {
            return redirect()->route('my-programs')
                ->with('success', 'Data program vokasi berhasil ditambahkan.');
        }

        return redirect()->route('programs.index')
            ->with('success', 'Data program vokasi berhasil ditambahkan.');
    }

    public function show(Program $program)
    {
        $this->authorize('view', $program);
        
        $program->load(['school', 'category', 'creator']);
        
        return view('programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $this->authorize('update', $program);
        
        $user = Auth::user();
        
        if ($user->isOperator()) {
            // Untuk operator: ambil kategori GLOBAL + kategori LOKAL sekolahnya
            $globalCategories = ProgramCategory::global()->orderBy('name')->get();
            $localCategories = ProgramCategory::local($user->school_id)->orderBy('name')->get();
            $categories = $globalCategories->merge($localCategories);
        } else {
            $categories = ProgramCategory::global()->orderBy('name')->get();
        }
        
        return view('programs.edit', compact('program', 'categories'));
    }

    public function update(Request $request, Program $program)
    {
        $this->authorize('update', $program);
        
        $validated = $request->validate([
            'category_id' => 'required|exists:program_categories,id',
            'program_name' => 'required|max:255',
            'description' => 'nullable',
            'student_count' => 'required|integer|min:0',
            'teacher_count' => 'nullable|integer|min:0',
            'facilities' => 'nullable',
            'products' => 'nullable',
            'achievements' => 'nullable',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['updated_by'] = Auth::id();
        
        $user = Auth::user();
        
        // Validasi: operator tidak bisa menggunakan kategori lokal milik sekolah lain
        if ($user->isOperator()) {
            $category = ProgramCategory::find($request->category_id);
            if (!$category->is_global && $category->school_id != $user->school_id) {
                return redirect()->back()
                    ->with('error', 'Anda tidak dapat menggunakan kategori dari sekolah lain.');
            }
        }

        $program->update($validated);

        // Redirect sesuai role
        if ($user->isOperator()) {
            return redirect()->route('my-programs')
                ->with('success', 'Data program vokasi berhasil diperbarui.');
        }

        return redirect()->route('programs.index')
            ->with('success', 'Data program vokasi berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $this->authorize('delete', $program);
        
        $program->delete();

        // Redirect sesuai role
        if (auth()->user()->isOperator()) {
            return redirect()->route('my-programs')
                ->with('success', 'Data program vokasi berhasil dihapus.');
        }

        return redirect()->route('programs.index')
            ->with('success', 'Data program vokasi berhasil dihapus.');
    }

    public function myPrograms(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        $query = Program::where('school_id', $user->school_id)
                    ->with('category');
        
        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where('program_name', 'like', '%' . $request->search . '%');
        }
        
        $programs = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Untuk operator: tampilkan kategori GLOBAL + kategori LOKAL sekolahnya di filter
        $globalCategories = ProgramCategory::global()->orderBy('name')->get();
        $localCategories = ProgramCategory::local($user->school_id)->orderBy('name')->get();
        $categories = $globalCategories->merge($localCategories);
        
        return view('programs.operator-index', compact('programs', 'categories'));
    }
    
    public function exportOperatorSchoolExcel()
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->back()->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        // Load school with programs
        $school = School::with('programs.category')->find($user->school_id);
        
        $export = new OperatorSchoolExport($school);
        return Excel::download($export, 'data-sekolah-' . $school->npsn . '.xlsx');
    }
    
    /**
     * Export program sekolah operator ke Excel (RAPI)
     */
    public function exportOperatorProgramsExcel()
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->back()->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        $export = new OperatorProgramsExport($user->school);
        return Excel::download($export, 'program-vokasi-' . $user->school->npsn . '.xlsx');
    }
    
    /**
     * Export data sekolah operator ke PDF
     */
    public function exportOperatorSchoolPdf()
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->back()->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        $school = School::with('programs.category')->find($user->school_id);
        $programs = $school->programs;
        $totalPrograms = $programs->count();
        $totalStudents = $programs->sum('student_count');
        
        $pdf = Pdf::loadView('schools.operator-pdf', compact('school', 'programs', 'totalPrograms', 'totalStudents'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('data-sekolah-' . $school->npsn . '.pdf');
    }
    
    /**
     * Export program sekolah operator ke PDF
     */
    public function exportOperatorProgramsPdf()
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->back()->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        $programs = Program::where('school_id', $user->school_id)
                    ->with('category')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        $school = $user->school;
        $totalPrograms = $programs->count();
        $totalStudents = $programs->sum('student_count');
        
        $pdf = Pdf::loadView('programs.operator-pdf', compact('programs', 'school', 'totalPrograms', 'totalStudents'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('program-vokasi-' . $school->npsn . '.pdf');
    }
}