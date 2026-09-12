<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Ajout d'une couleur à la volée depuis le sélecteur général du formulaire de variante —
     * évite de limiter l'admin aux quelques couleurs déjà enregistrées.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'hex_code' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $color = Color::firstOrCreate(
            ['hex_code' => $data['hex_code']],
            ['name' => $data['name']],
        );

        return response()->json($color);
    }
}
