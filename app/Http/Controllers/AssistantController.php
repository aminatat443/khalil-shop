<?php

namespace App\Http\Controllers;

use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AssistantController extends Controller
{
    public function __construct(private readonly AiAssistantService $assistant) {}

    /**
     * Assistant IA (FAQ boutique) — la conversation est tenue côté client (Alpine) et
     * renvoyée en entier à chaque message, pas de stockage serveur.
     */
    public function message(Request $request): JsonResponse
    {
        $data = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:20'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $reply = $this->assistant->reply($data['messages']);
        } catch (Throwable $e) {
            // Toute panne (clé invalide, timeout réseau, réponse inattendue de l'API...)
            // doit dégrader proprement plutôt que de faire planter la requête.
            report($e);

            return response()->json([
                'reply' => "Le service de l'assistant est momentanément indisponible. Réessayez plus tard ou contactez le service client.",
            ], 503);
        }

        return response()->json(['reply' => $reply]);
    }
}
