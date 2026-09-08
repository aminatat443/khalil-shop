<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Gestion des promotions planifiées (section 46 du cahier des charges).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Promotion::class);

        $promotions = Promotion::with(['category', 'product'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->input('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->whereHas('product', fn ($q) => $q->where('name', 'ilike', $term))
                        ->orWhereHas('category', fn ($q) => $q->where('name', 'ilike', $term));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.promotions.index', ['promotions' => $promotions]);
    }

    public function create(): View
    {
        $this->authorize('create', Promotion::class);

        return view('admin.promotions.form', [
            'promotion' => new Promotion(),
            'categories' => Category::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Promotion::class);

        Promotion::create($this->validated($request));

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion créée.');
    }

    public function edit(Promotion $promotion): View
    {
        $this->authorize('update', $promotion);

        return view('admin.promotions.form', [
            'promotion' => $promotion,
            'categories' => Category::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $this->authorize('update', $promotion);

        $promotion->update($this->validated($request));

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion mise à jour.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $this->authorize('delete', $promotion);

        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion supprimée.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
