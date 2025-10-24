<?php
// app/Domain/Checkout/LeadTimeService.php

namespace App\Domain\Checkout;

use App\Domain\Checkout\DTO\CartItemSelection;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductOptionValue;
use Illuminate\Support\Collection;

class LeadTimeService
{
    /**
     * Liefert die erforderliche Vorlaufzeit in TAGEN für EIN Cart-Item.
     * Regel:
     *   product.min_lead_days
     *   plus MAX über gewählte Option-Values:
     *       extra_lead_days  (+1 Tag, falls extra_lead_hours > 0)
     * KEINE Varianten, KEIN Branch-Default.
     */
    public function forItemDays(CartItemSelection $item, Branch $branch): int
    {
        /** @var Product $product */
        $product = Product::query()->findOrFail($item->productId);

        $baseDays = (int) ($product->min_lead_days ?? 0);

        $extraDays = 0;
        $valueIds = $item->optionValueIds ?? [];
        if (!empty($valueIds)) {
            $vals = ProductOptionValue::query()
                ->whereIn('id', $valueIds)
                ->get(['extra_lead_days', 'extra_lead_hours']);

            foreach ($vals as $v) {
                $d = (int) $v->extra_lead_days;
                // Falls Stunden gepflegt sind, auf den nächsten vollen Tag runden (hier: +1 Tag)
                if ((int)$v->extra_lead_hours > 0) {
                    $d += 1;
                }
                if ($d > $extraDays) {
                    $extraDays = $d;
                }
            }
        }

        return $baseDays + $extraDays;
    }

    /**
     * Vorlaufzeit in TAGEN für den gesamten Warenkorb = Maximum über alle Items.
     */
    public function forCartDays(Collection $items, Branch $branch): int
    {
        $max = 0;
        foreach ($items as $it) {
            $max = max($max, $this->forItemDays($it, $branch));
        }
        return $max;
    }
}
