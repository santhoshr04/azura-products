<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a paginated, searchable, filterable listing of products.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->search($request->string('search')->toString())
            ->ofCategory($request->string('category')->toString())
            ->ofStatus($request->string('status')->toString() ?: \App\Enums\Status::Active->value)
            ->sort($request->string('sort')->toString(), $request->string('direction')->toString())
            ->paginate(5)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('products.create', [
            'categories' => Category::query()->active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::query()->create($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$product->name}\" was created successfully.");
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()
                ->where(fn ($query) => $query
                    ->where('status', Status::Active->value)
                    ->orWhere('id', $product->category_id))
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$product->name}\" was updated successfully.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$name}\" was deleted successfully.");
    }
}
