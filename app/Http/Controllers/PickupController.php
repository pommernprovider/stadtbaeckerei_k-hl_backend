<?php

namespace App\Http\Controllers;

use App\Domain\Cart\CartService;
use App\Domain\Checkout\PickupWindowService;
use App\Http\Requests\PickupSelectRequest;
use App\Http\Requests\PickupWindowsRequest;
use App\Models\Branch;
use Carbon\CarbonImmutable;

class PickupController extends Controller
{
    public function __construct(
        private CartService $cart,
        private PickupWindowService $windows
    ) {}

    public function windows(PickupWindowsRequest $request)
    {
        $branch = Branch::findOrFail((int) $request->branch_id);

        // Optional: Max-Tage-voraus prüfen, sonst 422
        if ($branch->pickup_max_days_ahead !== null) {
            $reqDay = CarbonImmutable::parse($request->date)->startOfDay();
            $limit  = CarbonImmutable::today()->addDays($branch->pickup_max_days_ahead);
            if ($reqDay->gt($limit)) {
                return response()->json([
                    'date' => $request->date,
                    'windows' => [],
                    'message' => 'Datum liegt zu weit in der Zukunft.',
                ], 422);
            }
        }

        $state = $this->cart->get();
        $list = $this->windows->listForDate($branch, $request->date, $state->items);

        return response()->json([
            'date' => $request->date,
            'windows' => $list,
        ]);
    }

    public function select(PickupSelectRequest $request)
    {
        $this->cart->setPickup((int) $request->branch_id, $request->date, $request->window_start);
        return response()->json(['ok' => true]);
    }
}
