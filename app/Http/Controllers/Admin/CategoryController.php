<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store()
    {
        // TODO: Chapter — Category admin CRUD
    }

    public function edit(Category $category)
    {
        return Inertia::render('Admin/Categories/Edit', ['category' => $category]);
    }

    public function update(Category $category)
    {
        // TODO
    }

    public function destroy(Category $category)
    {
        // TODO
    }
}
