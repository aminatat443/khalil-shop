<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Gestion de la homepage (section 48 du cahier des charges) : hero, bannières,
     * sans intervention du développeur.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Banner::class);

        return view('admin.banners.index', ['banners' => Banner::orderBy('sort_order')->get()]);
    }

    public function create(): View
    {
        $this->authorize('create', Banner::class);

        return view('admin.banners.form', ['banner' => new Banner()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Banner::class);

        $request->validate(['image_file' => ['required', 'image', 'max:4096']]);

        Banner::create($this->validated($request));

        return redirect()->route('admin.banners.index')->with('status', 'Bannière créée.');
    }

    public function edit(Banner $banner): View
    {
        $this->authorize('update', $banner);

        return view('admin.banners.form', ['banner' => $banner]);
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $this->authorize('update', $banner);

        $banner->update($this->validated($request));

        return redirect()->route('admin.banners.index')->with('status', 'Bannière mise à jour.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->authorize('delete', $banner);

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('status', 'Bannière supprimée.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'placement' => ['required', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('image_file')) {
            $data['image'] = Storage::disk('public')->url($request->file('image_file')->store('banners', 'public'));
        }
        unset($data['image_file']);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
