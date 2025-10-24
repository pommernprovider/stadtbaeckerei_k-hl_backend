<?php
// app/Http/Controllers/CartWebController.php
namespace App\Http\Controllers;

use App\Domain\Checkout\DTO\CartItemSelection;
use App\Domain\Checkout\LeadTimeService;
use App\Models\Branch;
use App\Models\BranchOpeningHour;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class CartWebController extends Controller
{
    private function getCart(): array
    {
        return session('cart_web', ['items' => []]);
    }
    private function saveCart(array $cart): void
    {
        session(['cart_web' => $cart]);
    }

    public function index()
    {
        $cart = session('cart_web', ['items' => []]);

        $productMap = Product::query()
            ->whereIn('id', collect($cart['items'])->pluck('product_id'))
            ->get()->keyBy('id');

        $grand = 0.0;           // Brutto gesamt
        $subtotal = 0.0;        // Netto gesamt
        $taxTotal = 0.0;        // MwSt gesamt
        $taxByRate = [];        // MwSt je Steuersatz

        $items = collect($cart['items'])->map(function ($row, $i) use (&$grand, &$subtotal, &$taxTotal, &$taxByRate, $productMap) {
            $product   = $productMap[$row['product_id']] ?? null;
            $qty       = (int) ($row['qty'] ?? 1);
            $unitGross = (float) ($row['unit_price'] ?? ($product?->base_price ?? 0)); // inkl. Δ
            $rate      = (float) ($product?->tax_rate ?? 0); // z.B. 7.00 oder 19.00

            $lineGross = round($unitGross * $qty, 2);

            // Netto & MwSt aus Brutto herausrechnen
            if ($rate > 0) {
                $lineNet = round($lineGross / (1 + $rate / 100), 2);
            } else {
                $lineNet = $lineGross;
            }
            $lineVat = round($lineGross - $lineNet, 2);

            // Summen
            $grand    += $lineGross;
            $subtotal += $lineNet;
            $taxTotal += $lineVat;

            // Breakdown nach Steuersatz
            $key = number_format($rate, 2, '.', '');
            $taxByRate[$key] = ($taxByRate[$key] ?? 0) + $lineVat;

            return [
                'line'      => $i,
                'product'   => $product,
                'qty'       => $qty,
                'unit'      => $unitGross,   // Einzelpreis brutto
                'sum'       => $lineGross,   // Zeilensumme brutto
                'tax_rate'  => $rate,
                'net'       => $lineNet,
                'vat'       => $lineVat,
                'options'   => $row['options'] ?? [],
            ];
        });

        // Schöne Sortierung der Breakdown-Keys (z. B. 7.00, 19.00)
        ksort($taxByRate, SORT_NUMERIC);

        return view('shop.cart', [
            'items'     => $items,
            'grand'     => round($grand, 2),
            'subtotal'  => round($subtotal, 2),
            'taxTotal'  => round($taxTotal, 2),
            'taxByRate' => $taxByRate,
        ]);
    }



    public function add(Request $r)
    {
        $product = Product::query()
            ->with(['options.activeValues', 'tags'])
            ->findOrFail((int)$r->input('product_id'));

        // 1) Verfügbarkeit serverseitig absichern
        if (! $product->isPurchasable()) {
            return back()->withErrors([
                'product' => $product->available_from && $product->available_from->gt(now())
                    ? 'Dieser Artikel ist erst ab ' . $product->available_from->translatedFormat('d.m.Y') . ' verfügbar.'
                    : 'Dieser Artikel ist derzeit nicht verfügbar.'
            ])->withInput();
        }

        // 2) Eingaben validieren (wie gehabt, dynamisch nach Optionen)
        $qty = max(1, (int) $r->input('qty', 1));
        $inputOptions = $r->input('options', []);

        $rules = [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty'        => ['required', 'integer', 'min:1'],
            'options'    => ['array'],
        ];

        foreach ($product->options as $opt) {
            $key = "options.{$opt->id}";
            switch ($opt->type) {
                case 'select':
                case 'radio':
                    $ids = $opt->activeValues->pluck('id')->implode(',');
                    $rules[$key] = array_filter([
                        $opt->is_required ? 'required' : 'nullable',
                        "in:{$ids}",
                    ]);
                    break;
                case 'multi':
                    $ids = $opt->activeValues->pluck('id')->implode(',');
                    $rules[$key] = [$opt->is_required ? 'required' : 'nullable', 'array'];
                    $rules["{$key}.*"] = ["in:{$ids}"];
                    break;
                case 'text':
                    $rules[$key] = [$opt->is_required ? 'required' : 'nullable', 'string', 'max:255'];
                    break;
            }
        }

        $rules['line_note'] = [$product->notes_required ? 'required' : 'nullable', 'string', 'max:255'];

        $validated = $r->validate($rules, [
            'options.*.in'        => 'Ungültige Optionsauswahl.',
            'line_note.required'  => 'Bitte gib eine Notiz/Widmung an.',
        ]);

        // 3) Preis-Δ aus gewählten Values bestimmen (KEINE Leads mehr in Minuten)
        $optionSnapshots = [];
        $priceDeltaTotal = 0.0;

        foreach ($product->options as $opt) {
            $sel = $inputOptions[$opt->id] ?? null;

            if ($opt->type === 'text') {
                if ($sel !== null && $sel !== '') {
                    $optionSnapshots[] = [
                        'option_id'   => $opt->id,
                        'option_name' => $opt->name,
                        'value_id'    => null,
                        'value_label' => null,
                        'free_text'   => (string)$sel,
                        'price_delta' => 0.0,
                    ];
                }
                continue;
            }

            if ($sel === null || $sel === '' || $sel === []) {
                continue; // optional und nichts gewählt
            }

            $valueIds = is_array($sel) ? $sel : [$sel];
            $values = $opt->activeValues->whereIn('id', $valueIds);

            foreach ($values as $v) {
                $pd = (float)($v->price_delta ?? 0);
                $priceDeltaTotal += $pd;

                $optionSnapshots[] = [
                    'option_id'   => $opt->id,
                    'option_name' => $opt->name,
                    'value_id'    => $v->id,
                    'value_label' => $v->value,
                    'free_text'   => null,
                    'price_delta' => $pd,
                ];
            }
        }

        // synthetische Option für Zeilen-Notiz (falls vorhanden)
        $lineNote = (string)($validated['line_note'] ?? '');
        if ($lineNote !== '') {
            $optionSnapshots[] = [
                'option_id'   => null,
                'option_name' => 'Notiz',
                'value_id'    => null,
                'value_label' => null,
                'free_text'   => $lineNote,
                'price_delta' => 0.0,
            ];
        }

        // 4) Warenkorb aktualisieren
        $cart = session('cart_web', ['items' => []]);

        $cart['items'][] = [
            'product_id' => $product->id,
            'qty'        => $qty,
            'unit_price' => (float)$product->base_price + $priceDeltaTotal, // Basis + Δ der Auswahl
            'options'    => $optionSnapshots,
            // KEIN 'extra_lead' mehr – Lead wird später tagebasiert aus DB gerechnet
        ];

        session(['cart_web' => $cart]);

        return redirect()->route('cart.index')->with('ok', 'Zum Warenkorb hinzugefügt.');
    }



    public function update(Request $r)
    {
        $data = $r->validate([
            'line' => ['required', 'integer', 'min:0'],
            'qty'  => ['required', 'integer', 'min:0'],
        ]);

        $cart = session('cart_web', ['items' => []]);

        if (! isset($cart['items'][$data['line']])) {
            return back();
        }

        if ($data['qty'] === 0) {
            // entfernen
            array_splice($cart['items'], $data['line'], 1);
        } else {
            $cart['items'][$data['line']]['qty'] = $data['qty'];
        }

        session(['cart_web' => $cart]);
        return back();
    }

    public function remove(Request $r)
    {
        $data = $r->validate([
            'line' => ['required', 'integer', 'min:0'],
        ]);

        $cart = session('cart_web', ['items' => []]);

        if (isset($cart['items'][$data['line']])) {
            array_splice($cart['items'], $data['line'], 1);
            session(['cart_web' => $cart]);
        }
        return back();
    }

    public function pickupMeta(Request $r, LeadTimeService $lead)
    {
        $data = $r->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        $branch = Branch::findOrFail($data['branch_id']);

        // Warenkorb aus Session wie in CheckoutWebController@show()
        $cart = session('cart_web', ['items' => []]);

        // Cart → DTOs (nur das, was LeadTimeService braucht)
        $items = collect($cart['items'])->map(function ($row) {
            $optionValueIds = collect($row['options'] ?? [])->pluck('value_id')->filter()->values()->all();
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

        // Tagebasierte Lead-Zeit
        $leadDays = $lead->forCartDays($items, $branch);
        $today = CarbonImmutable::today();
        $earliestDate = $today->addDays($leadDays)->toDateString();

        // Öffnungszeiten (Wochensicht)
        $hours = BranchOpeningHour::query()
            ->where('branch_id', $branch->id)
            ->orderBy('weekday')
            ->get()
            ->map(function ($h) {
                return [
                    'weekday'   => (int) $h->weekday,  // 0=So..6=Sa
                    'is_closed' => (bool) $h->is_closed,
                    'opens_at'  => $h->opens_at,
                    'closes_at' => $h->closes_at,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'lead_days'      => $leadDays,
            'earliest_date'  => $earliestDate,     // YYYY-MM-DD
            'opening_hours'  => $hours,            // für kompakte Anzeige
            'today'          => $today->toDateString(),
        ]);
    }
}
