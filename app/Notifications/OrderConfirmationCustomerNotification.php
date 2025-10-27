<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationCustomerNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */


    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bestellbestätigung #' . $this->order->order_number)
            ->greeting('Hallo ' . ($this->order->customer_name ?? ''))
            ->line('Vielen Dank für Ihre Bestellung!')
            ->line('Bestellnummer: ' . $this->order->order_number)
            ->line('Abholung: ' . optional($this->order->pickup_at)->format('d.m.Y H:i'))
            ->line('Summe: ' . number_format($this->order->total_gross, 2, ',', '.') . ' €')
            ->line('Abholort: ' . optional($this->order->branch)->name)
            ->salutation('Ihre Stadtbäckerei KÜHL');
    }
}
