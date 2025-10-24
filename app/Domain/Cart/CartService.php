<?php

// app/Domain/Cart/CartService.php
namespace App\Domain\Cart;

use App\Domain\Checkout\DTO\CartItemSelection;
use App\Domain\Checkout\DTO\CartState;
use Illuminate\Support\Collection;

class CartService
{
    public const KEY = 'cart';

    public function get(): CartState
    {
        $raw = session(self::KEY, [
            'items' => [],
            'branch_id' => null,
            'pickup_date' => null,
            'pickup_window_start' => null,
        ]);

        $items = collect($raw['items'] ?? [])->map(
            fn($i) =>
            new CartItemSelection(
                productId: $i['product_id'],
                quantity: $i['quantity'],
                variantId: $i['variant_id'] ?? null,
                optionValueIds: $i['option_value_ids'] ?? [],
                freeTextByOptionId: $i['free_text'] ?? [],
            )
        );

        return new CartState(
            items: $items,
            branchId: $raw['branch_id'] ?? null,
            pickupDate: $raw['pickup_date'] ?? null,
            pickupWindowStart: $raw['pickup_window_start'] ?? null,
        );
    }

    public function set(CartState $state): void
    {
        session([self::KEY => [
            'items' => $state->items->map(fn($i) => [
                'product_id' => $i->productId,
                'quantity' => $i->quantity,
                'variant_id' => $i->variantId,
                'option_value_ids' => array_values($i->optionValueIds),
                'free_text' => $i->freeTextByOptionId,
            ])->values()->all(),
            'branch_id' => $state->branchId,
            'pickup_date' => $state->pickupDate,
            'pickup_window_start' => $state->pickupWindowStart,
        ]]);
    }

    public function addItem(CartItemSelection $item): void
    {
        $state = $this->get();

        // Merge gleicher Key
        $key = fn($i) => implode(':', [
            $i->productId,
            $i->variantId ?? 'null',
            implode(',', array_values($i->optionValueIds)),
            md5(json_encode($i->freeTextByOptionId)),
        ]);

        $map = $state->items->keyBy($key);
        $k = $key($item);

        if ($map->has($k)) {
            $existing = $map->get($k);
            $existing->quantity += $item->quantity;
            $map->put($k, $existing);
        } else {
            $map->put($k, $item);
        }

        $state->items = $map->values();
        $this->set($state);
    }

    public function updateItem(int $index, int $quantity): void
    {
        $state = $this->get();
        $items = $state->items->values();
        if (! isset($items[$index])) return;

        if ($quantity <= 0) {
            $items->forget($index);
        } else {
            $items[$index]->quantity = $quantity;
        }

        $state->items = $items->values();
        $this->set($state);
    }

    public function removeItem(int $index): void
    {
        $state = $this->get();
        $items = $state->items->values();
        if (! isset($items[$index])) return;

        $items->forget($index);
        $state->items = $items->values();
        $this->set($state);
    }

    public function setPickup(int $branchId, string $dateYmd, string $windowStartHms): void
    {
        $state = $this->get();
        $state->branchId = $branchId;
        $state->pickupDate = $dateYmd;
        $state->pickupWindowStart = $windowStartHms;
        $this->set($state);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }
}
