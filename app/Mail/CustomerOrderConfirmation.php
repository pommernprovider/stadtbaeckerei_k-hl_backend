<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;
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
            subject: 'Bestellbestätigung #' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */

    public function content(): Content
    {
        $order = $this->order->loadMissing([
            'items.options',
            'branch.openingHours',
            'branch.closures',
        ]);

        // Öffnungsinfo für den konkreten Abholtag (einfach & robust)
        $pickupDate = optional($order->pickup_at)?->toDateString();     // YYYY-MM-DD
        $weekday    = optional($order->pickup_at)?->dayOfWeekIso;       // 1..7 (Mo..So)

        $oh = null;   // "08:00–17:00" oder "geschlossen" oder null
        if ($order->branch) {
            // Closure am Tag?
            $closure = $order->branch->closures
                ->firstWhere('date', $pickupDate);

            if ($closure && $closure->full_day) {
                $oh = 'geschlossen';
            } elseif ($closure && (!$closure->full_day) && $closure->opens_at && $closure->closes_at) {
                $oh = substr($closure->opens_at, 0, 5) . '–' . substr($closure->closes_at, 0, 5);
            } else {
                // Standard-Öffnungszeit des Wochentags (0=So…6=Sa in DB; wir haben 1..7)
                $map = [7 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6]; // ISO zu DB
                $row = $order->branch->openingHours
                    ->firstWhere('weekday', $map[$weekday] ?? null);
                if ($row) {
                    $oh = $row->is_closed ? 'geschlossen'
                        : (substr($row->opens_at, 0, 5) . '–' . substr($row->closes_at, 0, 5));
                }
            }
        }

        // Google-Maps Link
        $branch = $order->branch;
        $mapsUrl = null;
        if ($branch) {
            if (!is_null($branch->lat) && !is_null($branch->lng)) {
                $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='
                    . $branch->lat . ',' . $branch->lng;
            } else {
                $q = trim(($branch->street ? $branch->street . ', ' : '')
                    . ($branch->zip ? $branch->zip . ' ' : '')
                    . ($branch->city ?? ''));
                if ($q !== '') $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($q);
            }
        }

        return new Content(
            markdown: 'mail.orders.confirmation',
            with: [
                'order'    => $order,
                'oh_text'  => $oh,
                'maps_url' => $mapsUrl,
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
