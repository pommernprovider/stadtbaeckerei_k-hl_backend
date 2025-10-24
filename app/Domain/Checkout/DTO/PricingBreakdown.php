<?php
// app/Domain/Checkout/DTO/PricingBreakdown.php
namespace App\Domain\Checkout\DTO;

class PricingBreakdown
{
    public function __construct(
        public float $unitGross,    // Stückpreis brutto
        public float $taxRate,      // z. B. 7.00
        public int   $quantity,
        public float $lineGross,    // unit * qty
        public float $lineNet,      // vom taxRate abgeleitet
        public float $lineTax,
        public array $components = [], // ['base'=>.., 'variant'=>.., 'options'=>[..]]
    ) {}
}
