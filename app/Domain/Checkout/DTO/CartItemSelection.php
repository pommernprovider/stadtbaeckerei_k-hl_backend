<?php

// app/Domain/Checkout/DTO/CartItemSelection.php
namespace App\Domain\Checkout\DTO;

class CartItemSelection
{
    public function __construct(
        public int $productId,
        public int $quantity = 1,
        public ?int $variantId = null,
        public array $optionValueIds = [],
        public array $freeTextByOptionId = [],
    ) {}
}
