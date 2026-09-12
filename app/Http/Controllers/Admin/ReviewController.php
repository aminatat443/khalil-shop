<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Modération des avis clients (section 39 du cahier des charges).
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Review::class);

        $search = function ($query) use ($request) {
            $term = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', fn ($q) => $q->where('name', 'ilike', $term))
                    ->orWhereHas('product', fn ($q) => $q->where('name', 'ilike', $term));
            });
        };

        $data = [
            'pending' => Review::with(['user', 'product'])
                ->where('is_approved', false)
                ->when($request->filled('q'), $search)
                ->latest()
                ->get(),
            'approved' => Review::with(['user', 'product'])
                ->where('is_approved', true)
                ->when($request->filled('q'), $search)
                ->latest()
                ->take(20)
                ->get(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.reviews.partials.lists', $data)->render(),
            ]);
        }

        return view('admin.reviews.index', $data);
    }

    public function approve(Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $review->update(['is_approved' => true]);

        return back()->with('status', 'Avis publié.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('status', 'Avis supprimé.');
    }
}
