<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedAdminNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sum = $this->order->grand_total ?? $this->order->total_gross ?? 0;
        $sumFormatted = number_format((float)$sum, 2, ',', '.') . ' €';

        return (new MailMessage)
            ->subject('Neue Bestellung #' . $this->order->order_number)
            ->greeting('Hallo Admin,')
            ->line('Es ist eine neue Bestellung eingegangen.')
            ->line('Bestellnummer: ' . $this->order->order_number)
            ->line('Abholung: ' . optional($this->order->pickup_at)->format('d.m.Y H:i'))
            ->line('Summe: ' . $sumFormatted)
            ->salutation('Ihre Stadtbäckerei KÜHL');
    }
}
