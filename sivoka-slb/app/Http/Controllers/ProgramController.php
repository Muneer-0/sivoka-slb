<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\School;
use App\Models\ProgramCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $categories = ProgramCategory::all();
        
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
        } else {
            $schools = School::orderBy('name')->get();
        }
        
        $categories = ProgramCategory::all();
        
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
        $validated['status'] = 'active'; // Default status
        
        $user = Auth::user();
        if ($user->isOperator() && $user->school_id != $request->school_id) {
            return redirect()->back()
                ->with('error', 'Anda hanya dapat menambah data untuk sekolah Anda sendiri.');
        }

        Program::create($validated);

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
        
        $categories = ProgramCategory::all();
        
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

        $program->update($validated);

        return redirect()->route('programs.index')
            ->with('success', 'Data program vokasi berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $this->authorize('delete', $program);
        
        $program->delete();

        return redirect()->route('programs.index')
            ->with('success', 'Data program vokasi berhasil dihapus.');
    }

    public function myPrograms()
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        $programs = Program::where('school_id', $user->school_id)
                    ->with('category')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('programs.my-programs', compact('programs'));
    }
}