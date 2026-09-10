<?php

namespace App\Mail;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Campaign $campaign,
        public readonly Collection $products,
        public readonly string $recipientName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->campaign->subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'campaign' => $this->campaign,
                'products' => $this->products,
                'recipientName' => $this->recipientName,
                'typeLabel' => Campaign::TYPES[$this->campaign->type] ?? $this->campaign->type,
            ],
        );
    }
}
