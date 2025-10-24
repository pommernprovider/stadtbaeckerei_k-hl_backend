<?php

// app/Domain/Checkout/DTO/CartState.php
namespace App\Domain\Checkout\DTO;

use Illuminate\Support\Collection;

class CartState
{
    public function __construct(
        /** @var Collection<CartItemSelection> */
        public Collection $items,
        public ?int $branchId = null,
        public ?string $pickupDate = null,      // Y-m-d
        public ?string $pickupWindowStart = null // H:i:s (Fensterstart)
    ) {}
}
