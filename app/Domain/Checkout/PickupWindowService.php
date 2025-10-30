<?php
// app/Domain/Checkout/PickupWindowService.php

namespace App\Domain\Checkout;

use App\Domain\Checkout\Exceptions\SlotUnavailableException;
use App\Models\Branch;
use App\Models\BranchClosure;
use App\Models\BranchOpeningHour;
use App\Models\BranchPickupWindow;
use App\Models\BranchPickupWindowOverride;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Collection;

class PickupWindowService
{
    public function __construct(private LeadTimeService $leadTime) {}

    /**
     * Rückgabe: array<[ 'start' => 'H:i:s', 'end' => 'H:i:s', 'label'=>string ]>
     */
    public function listForDate(Branch $branch, DateTimeInterface|string $date, Collection $cartItems): array
    {
        $day = $date instanceof DateTimeInterface
            ? CarbonImmutable::instance($date)->startOfDay()
            : CarbonImmutable::parse($date)->startOfDay();

        $today   = CarbonImmutable::today();
        $weekday = (int) $day->dayOfWeek; // 0=So

        // Vergangenheit sperren
        if ($day->lt($today)) {
            return [];
        }

        // --- NEU: Verfügbarkeitsfenster & Enforce-Logik ---
        $mins = [];
        $maxs = [];
        $hasUnconstrained = false;

        foreach ($cartItems as $it) {
            $p = \App\Models\Product::find($it->productId);
            if (! $p) continue;

            $from  = optional($p->available_from)->toDateString();
            $until = optional($p->available_until)->toDateString();

            if ($from)  $mins[] = $from;
            if ($until) $maxs[] = $until;

            if (! $from && ! $until) {
                $hasUnconstrained = true; // es gibt mind. einen „freien“ Artikel
            }
        }

        $hasAnyConstraint = !empty($mins) || !empty($maxs);
        $enforce = $hasAnyConstraint && ! $hasUnconstrained;

        // Schnittmenge nur anwenden, wenn enforce = true
        if ($enforce) {
            $cartMin = !empty($mins) ? max($mins) : null; // spätester from
            $cartMax = !empty($maxs) ? min($maxs) : null; // frühestes until
            $dayIso  = $day->toDateString();

            if ($cartMin && $dayIso < $cartMin) return [];
            if ($cartMax && $dayIso > $cartMax) return [];
        }

        // --- NEU: Lead-Time effektiv bestimmen ---
        if ($enforce) {
            // nur von-bis-Artikel im Korb -> Lead-Time über alle
            $leadDays = $this->leadTime->forCartDays($cartItems, $branch);
        } else {
            // gemischter Korb -> Lead-Time nur über unbeschränkte Artikel
            $unconstrained = $cartItems->filter(function ($it) {
                $p = \App\Models\Product::find($it->productId);
                return $p && is_null($p->available_from) && is_null($p->available_until);
            })->values();

            $leadDays = $unconstrained->isNotEmpty()
                ? $this->leadTime->forCartDays($unconstrained, $branch)
                : 0;
        }

        // Frühestes Datum durch Lead-Time
        $earliestDate = $today->addDays($leadDays)->startOfDay();
        if ($day->lt($earliestDate)) {
            return [];
        }

        // Cutoff (nur relevant, wenn der Nutzer HEUTE abholen möchte)
        if ($branch->order_cutoff_time && $day->isSameDay($today)) {
            $now = CarbonImmutable::now();
            $cutoffToday = $today->setTimeFromTimeString($branch->order_cutoff_time);
            if ($now->greaterThan($cutoffToday)) {
                return [];
            }
        }

        // Ganztägige Schließung?
        $closure = BranchClosure::query()
            ->where('branch_id', $branch->id)
            ->whereDate('date', $day->toDateString())
            ->first();

        if ($closure && $closure->full_day) {
            return [];
        }

        // Slots: Override (datumsspezifisch) → sonst Template (wochentagsbasiert)
        $rows = BranchPickupWindowOverride::query()
            ->where('branch_id', $branch->id)
            ->whereDate('date', $day->toDateString())
            ->where('is_active', true)
            ->orderBy('starts_at')
            ->get();

        if ($rows->isEmpty()) {
            $rows = BranchPickupWindow::query()
                ->where('branch_id', $branch->id)
                ->where('weekday', $weekday)
                ->where('is_active', true)
                ->orderBy('starts_at')
                ->get();
        }

        if ($rows->isEmpty()) {
            return [];
        }

        // Öffnungszeiten (optional)
        $opening = BranchOpeningHour::query()
            ->where('branch_id', $branch->id)
            ->where('weekday', $weekday)
            ->first();

        $hasOpening = $opening && !$opening->is_closed && $opening->opens_at && $opening->closes_at;

        $out = [];

        foreach ($rows as $w) {
            $start = $day->setTimeFromTimeString($w->starts_at);
            $end   = $day->setTimeFromTimeString($w->ends_at);

            // muss in Öffnungszeiten liegen (falls gepflegt)
            if ($hasOpening) {
                $openStart = $day->setTimeFromTimeString($opening->opens_at);
                $openEnd   = $day->setTimeFromTimeString($opening->closes_at);
                if ($start->lt($openStart) || $end->gt($openEnd)) {
                    continue;
                }
            }

            // Teil-Schließung (falls nicht ganztägig)
            if ($closure && !$closure->full_day && $closure->opens_at && $closure->closes_at) {
                $cStart = $day->setTimeFromTimeString($closure->opens_at);
                $cEnd   = $day->setTimeFromTimeString($closure->closes_at);
                if ($start->lt($cStart) || $end->gt($cEnd)) {
                    continue;
                }
            }

            $out[] = [
                'start' => $start->format('H:i:s'),
                'end'   => $end->format('H:i:s'),
                'label' => $w->label ?? ($start->format('H:i') . '–' . $end->format('H:i')),
            ];
        }

        return $out;
    }


    public function assertWindowSelectable(Branch $branch, DateTimeInterface|string $windowStart, Collection $cartItems): void
    {
        $start = $windowStart instanceof DateTimeInterface
            ? CarbonImmutable::instance($windowStart)
            : CarbonImmutable::parse($windowStart);

        $list = $this->listForDate($branch, $start, $cartItems);
        $hit = collect($list)->firstWhere('start', $start->format('H:i:s'));

        if (!$hit) {
            throw new SlotUnavailableException('Abholfenster nicht verfügbar.');
        }
    }
}
