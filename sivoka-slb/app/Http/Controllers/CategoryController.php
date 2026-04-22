<?php

namespace App\Http\Controllers;

use App\Models\ProgramCategory;
use Illuminate\Http\Request;
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
}