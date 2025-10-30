{{-- resources/views/shop/checkout.blade.php --}}
@extends('layouts.shop')
@section('title', 'Checkout')

@section('content')

  {{-- Header / Breadcrumb --}}
  <section class="border-b border-gray-200 bg-white">
    <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-6">
      <h1 class="text-2xl font-semibold text-gray-900">Checkout</h1>
      <nav aria-label="Breadcrumb" class="mt-2 text-sm">
        <ol class="flex items-center gap-2 text-gray-600">
          <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
          <li aria-hidden="true">/</li>
          <li class="text-gray-900 font-medium">Checkout</li>
        </ol>
      </nav>
    </div>
  </section>

  <section class="bg-white">
    <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- LEFT: Kontakt & Abholung --}}
        <div class="lg:col-span-8">
          <form id="checkout-form" action="{{ route('checkout.store') }}" method="post" class="space-y-6">
            @csrf

            {{-- Fehlermeldungen --}}
            @if ($errors->any())
              <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5 space-y-1">
                  @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            {{-- Kontakt --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5">
              <h2 class="text-lg font-semibold text-gray-900">Kontakt</h2>
              <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="name" class="mb-1 block text-sm font-medium text-gray-900">
                    Name <span class="text-red-600">*</span>
                  </label>
                  <input id="name" name="name" type="text" value="{{ old('name') }}" required
                         class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div>
                  <label for="email" class="mb-1 block text-sm font-medium text-gray-900">
                    E-Mail <span class="text-red-600">*</span>
                  </label>
                  <input id="email" name="email" type="email" value="{{ old('email') }}" required
                         class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div>
                  <label for="phone" class="mb-1 block text-sm font-medium text-gray-900">
                    Telefon <span class="text-red-600">*</span>
                  </label>
                  <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required
                         class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div>
                  <label for="adress" class="mb-1 block text-sm font-medium text-gray-900">
                    Adresse <span class="text-red-600">*</span>
                  </label>
                  <input id="adress" name="adress" type="text" value="{{ old('adress') }}" required
                         class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div>
                  <label for="tax" class="mb-1 block text-sm font-medium text-gray-900">
                    Postleitzahl <span class="text-red-600">*</span>
                  </label>
                  <input id="tax" name="tax" type="text" value="{{ old('tax') }}" required
                         class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div>
                  <label for="city" class="mb-1 block text-sm font-medium text-gray-900">
                    Ort <span class="text-red-600">*</span>
                  </label>
                  <input id="city" name="city" type="text" value="{{ old('city') }}" required
                         class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div class="sm:col-span-2">
                  <label for="customer_note" class="mb-1 block text-sm font-medium text-gray-900">Notiz</label>
                  <textarea id="customer_note" name="customer_note" rows="3" placeholder="Notiz"
                            class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">{{ old('customer_note') }}</textarea>
                </div>
              </div>
            </div>

            {{-- Abholung --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5">
              <h2 class="text-lg font-semibold text-gray-900">Abholung</h2>

              <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                  <label for="branch" class="mb-1 block text-sm font-medium text-gray-900">
                    Filiale <span class="text-red-600">*</span>
                  </label>
                  <select id="branch" name="branch_id" required
                          class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                    @foreach($branches as $b)
                      <option value="{{ $b->id }}" @selected(old('branch_id')==$b->id)>
                        {{ $b->name }} — {{ $b->street }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Öffnungszeiten --}}
                <div id="opening-hours" class="sm:col-span-2 hidden rounded-md border border-gray-200 bg-gray-50 p-4">
                  <div class="text-sm font-medium text-gray-900">Öffnungszeiten</div>
                  <ul id="opening-hours-list" class="mt-2 text-sm text-gray-700 space-y-0.5"></ul>
                </div>

                {{-- Vorlaufzeit-Hinweis --}}
                <div id="lead-hint" class="sm:col-span-2 hidden rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                  <span class="msg"></span>
                </div>

                <div>
                  <label for="date" class="mb-1 block text-sm font-medium text-gray-900">
                    Abholdatum <span class="text-red-600">*</span>
                  </label>
                  <input id="date" name="date" type="date" required min="{{ now()->toDateString() }}"
                         value="{{ old('date') }}"
                         class="block w-full rounded-md p-2 border border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>
              </div>

              {{-- Slots --}}
              <div id="slots-box" class="mt-4 hidden">
                <label class="mb-2 block text-sm font-medium text-gray-900">
                  Zeitfenster wählen <span class="text-red-600">*</span>
                </label>

                <div id="slots" class="space-y-2"></div>

                {{-- Alert für Fehler/Hinweise --}}
                <div id="slots-alert" class="mt-3 hidden rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                  <span class="font-medium mr-1">Hinweis:</span>
                  <span class="msg"></span>
                </div>
              </div>

              {{-- Datenschutz --}}
              <div class="mt-6">
                <label class="flex items-center gap-2 text-sm text-gray-800">
                  <input type="checkbox" name="agree" value="1" required {{ old('agree') ? 'checked' : '' }}
                         class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                  <span>Ich akzeptiere die Datenschutzbestimmungen.</span>
                </label>
              </div>

              <div class="mt-6">
                <button class="inline-flex items-center rounded-md bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:opacity-90">
                  Kostenpflichtig bestellen
                </button>
              </div>
            </div>
          </form>
        </div>

        {{-- RIGHT: Order Summary --}}
        <div class="lg:col-span-4">
          <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="text-lg font-semibold text-gray-900">Bestellübersicht</h2>

            <div class="mt-4 divide-y divide-gray-100">
              @forelse($items as $row)
                @php
                  $p = $row['product'];
                  $thumb = $p?->getFirstMediaUrl('product_main', 'thumb')
                    ?: $p?->getFirstMediaUrl('product_main')
                    ?: 'https://via.placeholder.com/80x80';
                @endphp
                <div class="py-3 flex gap-3">
                  <a href="{{ $p ? route('shop.product', $p) : '#' }}"
                     class="block h-16 w-16 shrink-0 overflow-hidden rounded-md border border-gray-200">
                    <img src="{{ $thumb }}" alt="{{ $p?->name ?? 'Artikel' }}" class="h-full w-full object-cover">
                  </a>
                  <div class="min-w-0 flex-1">
                    <a href="{{ $p ? route('shop.product', $p) : '#' }}"
                       class="line-clamp-2 font-medium text-gray-900 hover:underline">
                      {{ $p?->name ?? '—' }}
                    </a>
                    <div class="mt-1 text-sm text-gray-700">
                      {{ $row['qty'] }} × {{ number_format($row['unit'], 2, ',', '.') }} €
                    </div>

                    @if(!empty($row['options']))
                      <div class="mt-1 text-xs text-gray-600 space-y-0.5">
                        @foreach($row['options'] as $opt)
                          <div>
                            <strong>{{ $opt['option_name'] ?? 'Option' }}:</strong>
                            @if(!empty($opt['free_text']))
                              {{ $opt['free_text'] }}
                            @else
                              {{ $opt['value_label'] ?? '—' }}
                            @endif
                          </div>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
              @empty
                <div class="py-3 text-sm text-gray-600">Dein Warenkorb ist leer.</div>
              @endforelse
            </div>

            <dl class="mt-4 space-y-1 text-sm">
              <div class="flex justify-between">
                <dt class="text-gray-600">Zwischensumme (Netto)</dt>
                <dd class="font-medium text-gray-900">{{ number_format($subtotal, 2, ',', '.') }} €</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-gray-600">MwSt gesamt</dt>
                <dd class="font-medium text-gray-900">{{ number_format($taxTotal, 2, ',', '.') }} €</dd>
              </div>
            </dl>

            <div class="mt-4 border-t border-gray-200 pt-4">
              <div class="flex items-center justify-between text-base">
                <span class="font-medium text-gray-900">Gesamt (Brutto)</span>
                <span class="font-semibold text-gray-900">{{ number_format($grand, 2, ',', '.') }} €</span>
              </div>
            </div>

            <p class="mt-3 text-xs text-gray-500">Alle Preise inkl. gesetzl. MwSt.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const form      = document.getElementById('checkout-form');
    const branch    = document.getElementById('branch');
    const date      = document.getElementById('date');

    const hoursBox  = document.getElementById('opening-hours');
    const hoursList = document.getElementById('opening-hours-list');
    const leadHint  = document.getElementById('lead-hint');
    const leadMsg   = leadHint?.querySelector('.msg');

    const slotsBox  = document.getElementById('slots-box');
    const slots     = document.getElementById('slots');
    const alertBox  = document.getElementById('slots-alert');
    const alertMsg  = alertBox?.querySelector('.msg');

    let requestId = 0;
    const WEEKDAY_NAMES = ['So','Mo','Di','Mi','Do','Fr','Sa'];

    // Hilfen (Tailwind: hidden nutzen)
    function showEl(el, v) { if (!el) return; el.classList.toggle('hidden', !v); }
    function showAlert(msg) { if (!alertBox) return; alertMsg.textContent = msg||'Keine Abholfenster verfügbar.'; showEl(alertBox, true); }
    function hideAlert() { if (!alertBox) return; alertMsg.textContent = ''; showEl(alertBox, false); }
    function resetSlots(hideBox=false){ hideAlert(); if (slots) slots.innerHTML=''; if(hideBox) showEl(slotsBox, false); }

    function renderOpeningHours(hours) {
      if (!hoursList) return;
      hoursList.innerHTML = '';
      if (!Array.isArray(hours) || hours.length === 0) { showEl(hoursBox, false); return; }

      hours.forEach(h => {
        const li = document.createElement('li');
        const name = WEEKDAY_NAMES[h.weekday] ?? h.weekday;
        li.textContent = h.is_closed
          ? `${name}: geschlossen`
          : `${name}: ${(h.opens_at||'').substring(0,5)} – ${(h.closes_at||'').substring(0,5)}`;
        hoursList.appendChild(li);
      });
      showEl(hoursBox, true);
    }

    function setEarliestDate(earliestISO, leadDays) {
      if (!date) return;
      date.min = earliestISO;
      if (date.value && date.value < earliestISO) date.value = earliestISO;

      if (leadHint && leadMsg) {
        if ((leadDays ?? 0) > 0) {
          const d = new Date(earliestISO+'T00:00:00');
          leadMsg.textContent =
            `Frühestes Abholdatum: ${d.toLocaleDateString(undefined, { weekday:'short', day:'2-digit', month:'2-digit', year:'numeric' })}.`;
          showEl(leadHint, true);
        } else {
          showEl(leadHint, false);
        }
      }
    }

async function loadMeta() {
  const b = branch.value;
  if (!b) { showEl(hoursBox, false); showEl(leadHint, false); return; }

  try {
    const resp = await fetch('{{ route('cart.pickup.meta') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      body: JSON.stringify({ branch_id: b })
    });
    if (!resp.ok) return;

    const data = await resp.json();

    const fmt = (iso) => {
      if (!iso) return null;
      const [y,m,d] = iso.split('-').map(Number);
      return `${String(d).padStart(2,'0')}.${String(m).padStart(2,'0')}.${y}`;
    };

    renderOpeningHours(data.opening_hours);

    // Reset Hinweis & Alert
    if (leadHint && leadMsg) { leadMsg.textContent = ''; showEl(leadHint, false); }
    hideAlert();

    // Immer min (Lead-Time) setzen
    setEarliestDate(data.earliest_date, null);

    if (data.enforce) {
      // Optionales max (Schnittmenge)
      if (data.latest_date) {
        date.max = data.latest_date;
      } else {
        date.removeAttribute('max');
      }

      // Fixer Tag?
      if (data.fixed_date) {
        date.min = data.fixed_date;
        date.max = data.fixed_date;
        if (!date.value || date.value !== data.fixed_date) {
          date.value = data.fixed_date;
        }
        if (leadHint && leadMsg) {
          leadMsg.textContent = `Abholung ausschließlich am ${new Date(data.fixed_date+'T00:00:00').toLocaleDateString()}.`;
          showEl(leadHint, true);
        }
      } else {
        // Korrigiere value, falls außerhalb [min, max]
        const minV = date.min || null;
        const maxV = date.max || null;
        if (date.value && minV && date.value < minV) date.value = minV;
        if (date.value && maxV && date.value > maxV) date.value = maxV;
      }

      if (data.no_feasible) {
        showAlert('Die gewählten Artikel haben keine gemeinsame Verfügbarkeit. Bitte Warenkorb anpassen.');
      }
    } else {
      // Gemischt: nur Lead-Time-Minimum, KEIN max
      date.removeAttribute('max');

      // Falls aktuelles value < min → nachziehen
      if (date.value && date.min && date.value < date.min) {
        date.value = date.min;
      }

      // Lesbarer Hinweis
      const parts = (data.constrained || [])
        .map(c => {
          const n = c.name || 'Artikel';
          const from = fmt(c.from);
          const until = fmt(c.until);
          if (from && until && from === until)  return `„${n}“ nur am ${from}`;
          if (from && until)                    return `„${n}“ vom ${from} bis ${until}`;
          if (from)                             return `„${n}“ ab ${from}`;
          if (until)                            return `„${n}“ bis ${until}`;
          return null;
        })
        .filter(Boolean);

      if (parts.length) {
        const text = (parts.length === 1)
          ? `Hinweis: Für diesen Artikel gilt eine feste Verfügbarkeit: ${parts[0]}.`
          : `Hinweis: Für einige Artikel gelten feste Verfügbarkeiten: ${parts.join(' · ')}.`;
        if (leadHint && leadMsg) { leadMsg.textContent = text; showEl(leadHint, true); }
      }
    }
  } catch {
    // non-critical
  }
}

    async function loadSlots() {
      const currentId = ++requestId;
      const b = branch.value;
      const d = date.value;
      if (!b || !d) { resetSlots(true); return; }

      showEl(slotsBox, true);
      resetSlots(false);

      let resp;
      try {
        resp = await fetch('{{ route('cart.pickup.windows') }}', {
          method: 'POST',
          headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json' },
          body: JSON.stringify({ branch_id: b, date: d })
        });
      } catch {
        if (currentId !== requestId) return;
        showAlert('Verbindungsfehler beim Laden der Abholfenster.');
        return;
      }
      if (currentId !== requestId) return;

      if (!resp.ok) {
        let msg = 'Keine Abholfenster verfügbar.';
        try { const err = await resp.json(); if (err?.message) msg = err.message; } catch {}
        showAlert(msg);
        return;
      }

      let data;
      try { data = await resp.json(); } catch { showAlert('Unerwartete Serverantwort.'); return; }

      const windows = Array.isArray(data.windows) ? data.windows : [];
      if (windows.length === 0) { showAlert('Für diesen Tag sind keine Abholzeiten verfügbar.'); return; }

      hideAlert();
      const oldVal = @json(old('window_start'));
      let anySelected = false;

      windows.forEach((w, idx) => {
        const id = 'slot_' + idx;

        const label = document.createElement('label');
        label.setAttribute('for', id);
        label.className = 'flex items-center gap-3 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 hover:bg-gray-50';

        const input = document.createElement('input');
        input.type  = 'radio';
        input.name  = 'window_start';
        input.id    = id;
        input.value = w.start; // "H:i:s"
        input.className = 'h-4 w-4 border-gray-300 text-gray-900 focus:ring-gray-900';

        if (!anySelected && oldVal && oldVal === w.start) { input.checked = true; anySelected = true; }

        const span = document.createElement('span');
        span.textContent = w.label || w.start;

        label.appendChild(input);
        label.appendChild(span);
        slots.appendChild(label);
      });

      if (!anySelected) {
        const first = slots.querySelector('input[type=radio]');
        if (first) first.checked = true;
      }
    }

    // Guards & Events
    form.addEventListener('submit', (e) => {
      const chosen = form.querySelector('input[name="window_start"]:checked');
      if (!chosen) {
        e.preventDefault();
        if (branch.value && date.value) showEl(slotsBox, true);
        showAlert('Bitte ein Abholfenster auswählen, bevor Sie fortfahren.');
        (form.querySelector('#slots input[type=radio]') || date).focus();
      }
    });

    branch.addEventListener('change', async () => { await loadMeta(); await loadSlots(); });
    date.addEventListener('change', loadSlots);

    // Initial
    (async () => { await loadMeta(); })();
  });
  </script>

@endsection
