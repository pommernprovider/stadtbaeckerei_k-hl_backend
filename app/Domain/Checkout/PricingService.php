<?php
// app/Domain/Checkout/PricingService.php
namespace App\Domain\Checkout;

use App\Domain\Checkout\DTO\CartItemSelection;
use App\Domain\Checkout\DTO\PricingBreakdown;
use App\Models\Product;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class PricingService
{
    public function priceItem(CartItemSelection $item): PricingBreakdown
    {
        /** @var Product $product */
        $product = Product::query()->findOrFail($item->productId);

        $unit = (float) $product->base_price;
        $components = ['base' => $unit, 'variant' => 0.0, 'options' => []];

        if ($item->variantId) {
            /** @var ProductVariant $variant */
            $variant = ProductVariant::query()->where('product_id', $product->id)->findOrFail($item->variantId);
            if ($variant->price_override !== null) {
                $components['variant'] = (float) $variant->price_override - $unit;
                $unit = (float) $variant->price_override;
            }
        }

        if (! empty($item->optionValueIds)) {
            $vals = ProductOptionValue::query()
                ->whereIn('id', $item->optionValueIds)
                ->get(['id', 'price_delta']);

            foreach ($vals as $v) {
                $delta = (float) $v->price_delta;
                $unit += $delta;
                $components['options'][$v->id] = $delta;
            }
        }

        $qty = (int) $item->quantity;
        $lineGross = round($unit * $qty, 2);
        $taxRate = (float) $product->tax_rate;

        $lineNet = $taxRate > 0
            ? round($lineGross / (1 + $taxRate / 100), 2)
            : $lineGross;

        $lineTax = round($lineGross - $lineNet, 2);

        return new PricingBreakdown(
            unitGross: round($unit, 2),
            taxRate: $taxRate,
            quantity: $qty,
            lineGross: $lineGross,
            lineNet: $lineNet,
            lineTax: $lineTax,
            components: $components,
        );
    }

    public function priceCart(Collection $items): array
    {
        $lines = [];
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $grand = 0.0;

        foreach ($items as $it) {
            $b = $this->priceItem($it);
            $lines[] = $b;
            $subtotal += $b->lineNet;
            $taxTotal += $b->lineTax;
            $grand += $b->lineGross;
        }

        return [
            'lines' => $lines,
            'subtotal' => round($subtotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($grand, 2),
        ];
    }
}
