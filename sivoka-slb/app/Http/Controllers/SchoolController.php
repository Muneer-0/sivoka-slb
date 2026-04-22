<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', School::class);
        
        $query = School::withCount('programs');
        
        // Filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('npsn', 'like', '%' . $request->search . '%');
            });
        }
        
        $schools = $query->orderBy('city')->orderBy('name')->paginate(15);
        
        $cities = School::distinct('city')->orderBy('city')->pluck('city');
        $statuses = ['negeri' => 'Negeri', 'swasta' => 'Swasta'];
        
        return view('schools.index', compact('schools', 'cities', 'statuses'));
    }

    public function create()
    {
        $this->authorize('create', School::class);
        
        return view('schools.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', School::class);
        
        $validated = $request->validate([
            'npsn' => 'required|unique:schools|size:8',
            'name' => 'required|max:255',
            'address' => 'required',
            'district' => 'required',
            'city' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'headmaster' => 'nullable',
            'status' => 'required|in:negeri,swasta',
            'accreditation' => 'nullable'
        ]);

        School::create($validated);

        return redirect()->route('schools.index')
            ->with('success', 'Data SLB berhasil ditambahkan.');
    }

    public function show(School $school)
    {
        $this->authorize('view', $school);
        
        $school->load(['programs.category']);
        
        return view('schools.show', compact('school'));
    }

    public function edit(School $school)
    {
        $this->authorize('update', $school);
        
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $this->authorize('update', $school);
        
        $validated = $request->validate([
            'npsn' => 'required|unique:schools,npsn,' . $school->id . '|size:8',
            'name' => 'required|max:255',
            'address' => 'required',
            'district' => 'required',
            'city' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'headmaster' => 'nullable',
            'status' => 'required|in:negeri,swasta',
            'accreditation' => 'nullable'
        ]);

        $school->update($validated);

        return redirect()->route('schools.index')
            ->with('success', 'Data SLB berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $this->authorize('delete', $school);
        
        $school->delete();

        return redirect()->route('schools.index')
            ->with('success', 'Data SLB berhasil dihapus.');
    }

    public function mySchool()
    {
        $user = Auth::user();
        
        if (!$user->school) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai operator SLB.');
        }
        
        return redirect()->route('schools.show', $user->school);
    }
}