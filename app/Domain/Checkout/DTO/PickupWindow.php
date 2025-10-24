<?php
// app/Domain/Checkout/DTO/PickupWindow.php
namespace App\Domain\Checkout\DTO;

use Carbon\CarbonImmutable;

class PickupWindow
{
    public function __construct(
        public CarbonImmutable $start,    // exaktes Datum+Zeit (Fensterstart)
        public CarbonImmutable $end,      // exaktes Datum+Zeit (Fensterende)
        public ?int $capacity = null,
        public int $used = 0,
        public ?string $label = null,
        public bool $isActive = true,
    ) {}
}
