<?php

namespace App\Http\Controllers;

use App\Models\ProgramCategory;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ProgramCategory::class);
        
        $categories = ProgramCategory::withCount('programs')
                      ->orderBy('name')
                      ->paginate(15);
        
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', ProgramCategory::class);
        
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', ProgramCategory::class);
        
        $validated = $request->validate([
            'name' => 'required|unique:program_categories|max:255',
            'description' => 'nullable',
            'icon' => 'nullable'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_global'] = true; // Kategori dari admin adalah global

        ProgramCategory::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori program vokasi berhasil ditambahkan.');
    }

    public function show(ProgramCategory $category)
    {
        $this->authorize('view', $category);
        
        $category->load('programs.school');
        
        return view('categories.show', compact('category'));
    }

    public function edit(ProgramCategory $category)
    {
        $this->authorize('update', $category);
        
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, ProgramCategory $category)
    {
        $this->authorize('update', $category);
        
        $validated = $request->validate([
            'name' => 'required|unique:program_categories,name,' . $category->id . '|max:255',
            'description' => 'nullable',
            'icon' => 'nullable'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori program vokasi berhasil diperbarui.');
    }

    public function destroy(ProgramCategory $category)
    {
        $this->authorize('delete', $category);
        
        if ($category->programs()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki program vokasi.');
        }
        
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori program vokasi berhasil dihapus.');
    }

    // ==================== METHOD UNTUK OPERATOR (TAMBAHKAN INI) ====================

    /**
     * Store a new local category (untuk operator)
     * Kategori lokal hanya untuk sekolah operator tersebut
     */
 /**
 * Store a new local category (untuk operator)
 * Kategori lokal hanya untuk sekolah operator tersebut
 */
public function storeLocal(Request $request)
{
    // Selalu kembalikan JSON untuk endpoint ini
    try {
        $request->validate([
            'name' => 'required|max:100|unique:program_categories,name',
            'description' => 'nullable|max:255',
        ]);

        $user = Auth::user();
        
        // Hanya operator yang bisa membuat kategori lokal
        if (!$user->isOperator()) {
            return response()->json([
                'success' => false, 
                'error' => 'Hanya operator yang dapat menambah kategori lokal'
            ], 403);
        }

        // Pastikan operator memiliki sekolah
        if (!$user->school_id) {
            return response()->json([
                'success' => false, 
                'error' => 'Anda belum terdaftar sebagai operator SLB'
            ], 400);
        }

        $category = ProgramCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
            'icon' => 'bi bi-tag',
            'is_global' => false,
            'school_id' => $user->school_id,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'id' => $category->id,
            'name' => $category->name,
            'message' => 'Kategori "' . $category->name . '" berhasil ditambahkan!'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'error' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Get categories for operator dropdown (API)
     * Mengembalikan kategori global + kategori lokal milik sekolah operator
     */
    public function getCategoriesForOperator()
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $globalCategories = ProgramCategory::global()->orderBy('name')->get()->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'type' => 'global'
            ];
        });
        
        $localCategories = ProgramCategory::local($user->school_id)->orderBy('name')->get()->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'type' => 'local'
            ];
        });
        
        return response()->json([
            'global' => $globalCategories,
            'local' => $localCategories
        ]);
    }
}