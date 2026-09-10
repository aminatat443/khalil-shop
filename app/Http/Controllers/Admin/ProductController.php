<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Gestion des produits (section 41 du cahier des charges).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::query()
            ->with(['category', 'images' => fn ($q) => $q->orderBy('sort_order')])
            ->withCount('variants')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'ilike', '%'.$request->input('q').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->input('category_id')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::whereNotNull('parent_id')->orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $request->validate([
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            $disk = Setting::mediaDisk();
            $sortOrder = 0;

            foreach ($request->file('images') as $file) {
                $path = $file->store('products', $disk);

                $product->images()->create([
                    'url' => Storage::disk($disk)->url($path),
                    'public_id' => $disk === 'cloudinary' ? $path : null,
                    'alt' => $product->name,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produit créé.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        $product->load(['images' => fn ($q) => $q->orderBy('sort_order'), 'variants.color', 'variants.size']);

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::whereNotNull('parent_id')->orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $this->validated($request, $product->id);

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $product->update($data);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produit mis à jour.');
    }

    /**
     * Bascule rapide "vedette" (slider du hero) / "nouveauté", directement depuis la liste,
     * sans passer par le formulaire d'édition.
     */
    public function toggleFlag(Request $request, Product $product, string $flag): RedirectResponse
    {
        $this->authorize('update', $product);

        abort_unless(in_array($flag, ['is_featured', 'is_new', 'is_promo'], true), 404);

        $product->update([$flag => ! $product->$flag]);

        return back();
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Produit supprimé.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'model' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'old_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_new' => ['sometimes', 'boolean'],
            'is_promo' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        foreach (['is_new', 'is_promo', 'is_featured', 'is_active'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        return $data;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }
}
