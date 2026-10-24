<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('categories.index', [
            'categories' => $request->user()->categories()->withCount('tasks')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50',
                Rule::unique('categories')->where('user_id', $request->user()->id)],
        ], attributes: ['name' => 'nama kategori']);

        $request->user()->categories()->create($data);

        return back()->with('status', 'Kategori ditambahkan.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        // Tugas di dalamnya tidak ikut terhapus; kategorinya menjadi kosong.
        $category->delete();

        return back()->with('status', 'Kategori dihapus.');
    }
}
