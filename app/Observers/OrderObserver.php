<?php

namespace App\Observers;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderConfirmationCustomerNotification;
use App\Notifications\OrderPlacedAdminNotification;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Sicherstellen, dass die Benachrichtigungen erst nach Commit ausgeführt werden
        DB::afterCommit(function () use ($order) {
            // 🧁 Alle Filament-User mit E-Mail abrufen
            $recipients = User::query()
                ->whereNotNull('email')
                ->get();

            // 1️⃣ Filament In-App Notification (Glocke)
            if ($recipients->isNotEmpty()) {
                FilamentNotification::make()
                    ->title('Neue Bestellung #' . $order->order_number)
                    ->body(
                        "Summe: " . number_format($order->total_gross, 2, ',', '.') . " €\n" .
                            "Abholung: " . optional($order->pickup_at)->format('d.m.Y H:i')
                    )
                    ->icon('heroicon-o-shopping-bag')
                    ->success()
                    ->actions([
                        Action::make('Öffnen')
                            ->url(OrderResource::getUrl('view', ['record' => $order], panel: 'verwaltung'))
                            ->button(),
                    ])
                    ->sendToDatabase($recipients);
            }

            Notification::route('mail', 'paul.koch@pommernprovider.de')
                ->notify(new OrderPlacedAdminNotification($order));

            // 3️⃣ Kunden-Mail senden
            if (!empty($order->customer_email)) {
                Notification::route('mail', $order->customer_email)
                    ->notify(new OrderConfirmationCustomerNotification($order));
            }
        });
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
