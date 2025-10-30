<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderNew extends Mailable
{
    use Queueable, SerializesModels;

    public bool $afterCommit = true;
    /**
     * Create a new message instance.
     */
    public function __construct(public Order $order) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Neue Bestellung #' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $order = $this->order->loadMissing([
            'branch',
            'items.options',   // order_items + order_item_options
        ]);

        // Labels für Abholfenster
        $pickupDate   = optional($order->pickup_at)?->format('d.m.Y');
        $pickupWindow = $order->pickup_window_label
            ?: trim(($order->pickup_at?->format('H:i') ?? '') . ($order->pickup_end_at ? '–' . $order->pickup_end_at->format('H:i') : ''))
            ?: '–';

        return new Content(
            markdown: 'mail.orders.admin_new',
            with: [
                'order'         => $order,
                'pickup_date'   => $pickupDate,
                'pickup_window' => $pickupWindow,
                // Summen gemäß Migration/Spaltennamen
                'subtotal'      => (float) $order->subtotal,
                'tax_total'     => (float) $order->tax_total,
                'grand_total'   => (float) $order->grand_total,
                'currency'      => $order->currency ?? 'EUR',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
