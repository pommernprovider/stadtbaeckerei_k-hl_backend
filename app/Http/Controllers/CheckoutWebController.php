<?php
// app/Http/Controllers/CheckoutWebController.php
namespace App\Http\Controllers;

use App\Domain\Checkout\DTO\CartItemSelection;
use App\Http\Requests\CheckoutRequest;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Domain\Checkout\PickupWindowService;
use App\Domain\Checkout\Exceptions\SlotUnavailableException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutWebController extends Controller
{
    public function __construct(private PickupWindowService $windows) {}

    // app/Http/Controllers/CheckoutWebController.php

    public function show()
    {
        $branches = Branch::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // --- Warenkorb in Kurzform für Summary (wie Cart) ---
        $cart = session('cart_web', ['items' => []]);

        $productMap = Product::query()
            ->whereIn('id', collect($cart['items'])->pluck('product_id'))
            ->get()->keyBy('id');

        $grand = 0.0;     // Brutto gesamt
        $subtotal = 0.0;  // Netto gesamt
        $taxTotal = 0.0;  // MwSt gesamt

        $items = collect($cart['items'])->map(function ($row) use (&$grand, &$subtotal, &$taxTotal, $productMap) {
            $product   = $productMap[$row['product_id']] ?? null;
            $qty       = (int) ($row['qty'] ?? 1);
            $unitGross = (float) ($row['unit_price'] ?? ($product?->base_price ?? 0));
            $rate      = (float) ($product?->tax_rate ?? 0);

            $lineGross = round($unitGross * $qty, 2);
            $lineNet   = $rate > 0 ? round($lineGross / (1 + $rate / 100), 2) : $lineGross;
            $lineVat   = round($lineGross - $lineNet, 2);

            $grand    += $lineGross;
            $subtotal += $lineNet;
            $taxTotal += $lineVat;

            return [
                'product' => $product,
                'qty'     => $qty,
                'unit'    => $unitGross, // Brutto
                'sum'     => $lineGross, // Brutto
                'net'     => $lineNet,
                'vat'     => $lineVat,
                'tax_rate' => $rate,
                'options' => $row['options'] ?? [],
            ];
        });

        return view('shop.checkout', compact('branches', 'items', 'subtotal', 'taxTotal', 'grand'));
    }


    public function store(CheckoutRequest $r)
    {
        // 1) Warenkorb prüfen
        $cart = session('cart_web', ['items' => []]);
        if (empty($cart['items'])) {
            return back()->withErrors(['cart' => 'Warenkorb ist leer.'])->withInput();
        }

        // 2) Validierte Daten holen
        $data = $r->validated();

        $branch = Branch::findOrFail((int) $data['branch_id']);
        $windowStart = CarbonImmutable::parse($data['date'] . ' ' . $data['window_start']);

        // 3) Items -> DTO inkl. Option-Value-IDs (für LeadTime / Slot-Check)
        $items = collect($cart['items'])->map(function ($row) {
            $optionValueIds = collect($row['options'] ?? [])
                ->pluck('value_id')
                ->filter()   // nur echte IDs
                ->values()
                ->all();

            $freeTextByOptionId = collect($row['options'] ?? [])
                ->filter(fn($o) => !empty($o['free_text']) && !empty($o['option_id']))
                ->mapWithKeys(fn($o) => [$o['option_id'] => (string) $o['free_text']])
                ->all();

            return new CartItemSelection(
                productId: (int) $row['product_id'],
                quantity: (int) $row['qty'],
                variantId: null,
                optionValueIds: $optionValueIds,
                freeTextByOptionId: $freeTextByOptionId,
            );
        });

        // 4) Slot serverseitig prüfen
        try {
            $this->windows->assertWindowSelectable($branch, $windowStart, $items);
        } catch (SlotUnavailableException $e) {
            return back()
                ->withErrors(['window_start' => 'Das gewählte Abholfenster ist nicht mehr verfügbar. Bitte neu wählen.'])
                ->withInput();
        }

        // 5) Label & Endzeit (statt Dauer)
        $pair  = $this->resolveWindowPair($branch, $windowStart);
        $end   = $pair['end']   ?? $windowStart->addMinutes(120); // defensiver Fallback
        $label = $pair['label'] ?? ($windowStart->format('H:i') . '–' . $end->format('H:i'));

        // 6) Pricing auf Basis von unit_price aus dem Cart (Basis + ΣΔ)
        $subtotal = 0.0;
        $tax      = 0.0;
        $grand    = 0.0;

        // für Persist
        $lines = [];

        foreach ($cart['items'] as $row) {
            $p    = Product::findOrFail($row['product_id']);
            $qty  = (int) $row['qty'];
            $unit = (float) ($row['unit_price'] ?? $p->base_price);

            $gross = round($unit * $qty, 2);
            $net   = $p->tax_rate > 0 ? round($gross / (1 + $p->tax_rate / 100), 2) : $gross;
            $vat   = round($gross - $net, 2);

            $subtotal += $net;
            $tax      += $vat;
            $grand    += $gross;

            $lines[] = [
                'product'   => $p,
                'qty'       => $qty,
                'unit'      => $unit,
                'gross'     => $gross,
                'tax_rate'  => $p->tax_rate,
                'options'   => $row['options'] ?? [],
            ];
        }

        $branchNumber = (int) $branch->number; // z.B. 9091
        $number = Order::generateOrderNumber($branchNumber, $windowStart);

        // 7) Persist
        $order = DB::transaction(function () use ($data, $number, $branch, $windowStart, $end, $label, $subtotal, $tax, $grand, $lines) {
            $order = Order::create([
                'id'                 => (string) Str::ulid(),
                'order_number'       => $number,
                'status'             => 'pending',
                'branch_id'          => $branch->id,
                'pickup_at'          => $windowStart->toDateTimeString(),
                'pickup_end_at'      => $end->toDateTimeString(),
                'pickup_window_label' => $label,

                'customer_name'      => $data['name'],
                'customer_email'     => $data['email'],
                'customer_phone'     => $data['phone'],
                'customer_adress'    => $data['adress'],
                'customer_tax'       => $data['tax'],
                'customer_city'      => $data['city'],
                'customer_note'      => $data['customer_note'] ?? null,

                'subtotal'           => round($subtotal, 2),
                'tax_total'          => round($tax, 2),
                'grand_total'        => round($grand, 2),
                'currency'           => 'EUR',
                'locale'             => app()->getLocale(),
                'confirmation_token' => (string) Str::uuid(),
            ]);

            foreach ($lines as $l) {
                $item = $order->items()->create([
                    'product_id'            => $l['product']->id,
                    'product_name_snapshot' => $l['product']->name,
                    'variant_name_snapshot' => null,
                    'price_snapshot'        => $l['unit'],
                    'tax_rate_snapshot'     => $l['tax_rate'],
                    'quantity'              => $l['qty'],
                    'line_total'            => $l['gross'],
                ]);

                foreach ($l['options'] as $opt) {
                    $item->options()->create([
                        'option_name_snapshot' => $opt['option_name'] ?? '',
                        'value_label_snapshot' => $opt['value_label'] ?? null,
                        'free_text'            => $opt['free_text'] ?? null,
                        'price_delta_snapshot' => (float)($opt['price_delta'] ?? 0),

                    ]);
                }
            }

            return $order;
        });

        // 8) Cart leeren & weiter
        session()->forget('cart_web');

        return redirect()->route('checkout.thanks', $order->order_number);
    }

    public function thanks(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('shop.thanks', compact('order'));
    }

    private function resolveWindowPair(Branch $branch, CarbonImmutable $start): ?array
    {
        $date = $start->toDateString();
        $startH = $start->format('H:i:s');

        // Override (datumsspezifisch)
        $override = $branch->pickupWindowOverrides()
            ->whereDate('date', $date)
            ->where('starts_at', $startH)
            ->first();

        if ($override) {
            $end = $start->setTimeFromTimeString($override->ends_at);
            if ($end->lte($start)) $end = $end->addDay(); // robust
            return ['start' => $start, 'end' => $end, 'label' => $override->label];
        }

        // Template (wochentagsbasiert)
        $tpl = $branch->pickupWindows()
            ->where('weekday', $start->dayOfWeek) // 0..6
            ->where('starts_at', $startH)
            ->first();

        if ($tpl) {
            $end = $start->setTimeFromTimeString($tpl->ends_at);
            if ($end->lte($start)) $end = $end->addDay();
            return ['start' => $start, 'end' => $end, 'label' => $tpl->label];
        }

        return null;
    }

    private function resolveWindowDuration(Branch $branch, CarbonImmutable $start): int
    {
        $pair = $this->resolveWindowPair($branch, $start);
        if (! $pair) {
            return 120; // Fallback, sollte eigentl. nie greifen, wenn vorher validiert wurde
        }

        // Immer positiv
        return abs($pair['end']->diffInMinutes($pair['start'], false));
    }

    private function resolveWindowLabel(Branch $branch, CarbonImmutable $start): string
    {
        $pair = $this->resolveWindowPair($branch, $start);
        if (! $pair) {
            // defensiv aus Start + Defaultdauer erzeugen
            $end = $start->addMinutes(120);
            return $start->format('H:i') . '–' . $end->format('H:i');
        }

        if (!empty($pair['label'])) {
            return $pair['label'];
        }

        return $pair['start']->format('H:i') . '–' . $pair['end']->format('H:i');
    }
}
