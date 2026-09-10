<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Mail\CampaignMail;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CampaignController extends Controller
{
    /**
     * Emails de campagne (nouveautés, promotion, réassort…) envoyés à tous les clients inscrits.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Campaign::class);

        return view('admin.campaigns.index', [
            'campaigns' => Campaign::with('sender')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Campaign::class);

        return view('admin.campaigns.form', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Campaign::class);

        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', array_keys(Campaign::TYPES))],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $recipients = User::where('role', Role::Client)->whereNotNull('email_verified_at')->get(['id', 'name', 'email']);

        $campaign = Campaign::create([
            ...$data,
            'recipients_count' => $recipients->count(),
            'sent_by' => auth()->id(),
        ]);

        $products = $campaign->products();

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->queue(new CampaignMail($campaign, $products, $recipient->name));
        }

        return redirect()->route('admin.campaigns.index')->with('status', 'Campagne envoyée à '.$recipients->count().' client(s).');
    }
}
