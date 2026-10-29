<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a paginated, searchable listing of categories.
     */
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->search($request->string('search')->toString())
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::query()->create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', "Category \"{$category->name}\" was created successfully.");
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $category->loadCount('products');

        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', "Category \"{$category->name}\" was updated successfully.");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $products = $category->products()->count();

        if ($products > 0) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Cannot delete \"{$category->name}\" while {$products} product(s) still belong to it.");
        }

        $name = $category->name;

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "Category \"{$name}\" was deleted successfully.");
    }
}
