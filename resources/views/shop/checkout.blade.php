{{-- resources/views/shop/checkout.blade.php --}}
@extends('layouts.shop')
@section('title', 'Checkout')

@section('content')

    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        <h1 class="page-name">Checkout</h1>
                        <ol class="breadcrumb">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li class="active">Checkout</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="page-wrapper">
        <div class="checkout shopping">
            <div class="container">
                <div class="row">

                    {{-- LEFT: Kontakt & Abholung --}}
                    <div class="col-md-8">
                        <form id="checkout-form" action="{{ route('checkout.store') }}" method="post" class="checkout-form">
                            @csrf

                            {{-- Fehlermeldungen --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="m-b-0">
                                        @foreach ($errors->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Kontakt --}}
                            <div class="contact-form">
                                <h4 class="widget-title">Kontakt</h4>
                                <div class="form-group">
                                    <label for="email">Name <span class="required">*</span></label>
                                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="email">E-Mail <span class="required">*</span></label>
                                    <input id="email" name="email" type="email" class="form-control"
                                        value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Telefon <span class="required">*</span></label>
                                    <input id="phone" name="phone" type="text" class="form-control"
                                        value="{{ old('phone') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="adress">Adresse <span class="required">*</span></label>
                                    <input id="adress" name="adress" type="text" class="form-control"
                                        value="{{ old('adress') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="tax">Postleitzahl <span class="required">*</span></label>
                                    <input id="tax" name="tax" type="text" class="form-control"
                                        value="{{ old('tax') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="city">Ort <span class="required">*</span></label>
                                    <input id="city" name="city" type="text" class="form-control"
                                        value="{{ old('city') }}" required>
                                </div>

                                <div class="form-group">
                                    <textarea name="customer_note" class="form-control" rows="3"
                                        placeholder="Notiz">{{ old('customer_note') }}</textarea>
                                </div>
                            </div>

                           {{-- Abholung --}}
                            <div class="checkout-form">
                            <h4 class="widget-title">Abholung</h4>

                            <div class="form-group">
                                <label for="branch">Filiale <span class="required">*</span></label>
                                <select id="branch" name="branch_id" class="form-control" required>
                                    @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }} — {{ $b->city }}</option>
                                    @endforeach
                                </select>
                                </div>

                                {{-- Öffnungszeiten der Filiale --}}
                                <div id="opening-hours" class="panel panel-default" style="display:none;">
                                <div class="panel-heading"><strong>Öffnungszeiten</strong></div>
                                <div class="panel-body">
                                    <ul class="list-unstyled m-b-0" id="opening-hours-list"></ul>
                                </div>
                                </div>

                                {{-- Hinweis: frühestes Abholdatum basierend auf Vorlaufzeit --}}
                                <div id="lead-hint" class="alert alert-info" role="alert" style="display:none;">
                                <i class="tf-ion-information-circled"></i>
                                <span class="msg"></span>
                                </div>

                                <div class="form-group">
                                <label for="date">Abholdatum <span class="required">*</span></label>
                                <input id="date" name="date" type="date" class="form-control" required min="{{ now()->toDateString() }}">
                            </div>


                            {{-- Slots-Box wird erst angezeigt, wenn Filiale + Datum gewählt und Slots geladen sind --}}
                            <div id="slots-box" class="mt-2" style="display:none;">
                                <label>Zeitfenster wählen <span class="required">*</span></label>
                                <div id="slots" class="space-y-2"></div>

                                {{-- Alert für Fehler/Hinweise --}}
                                <div id="slots-alert" class="alert alert-danger alert-common mt-10" role="alert" style="display:none;">
                                <i class="tf-ion-close-circled"></i>
                                <span class="mr-5">Hinweis:</span>
                                <span class="msg"></span>
                                </div>
                            </div>

                            <div class="checkbox">
                                <label>
                                <input type="checkbox" name="agree" value="1" required {{ old('agree') ? 'checked' : '' }}>
                                Ich akzeptiere die Datenschutzbestimmungen.
                                </label>
                            </div>

                            <div>
                                <button class="btn btn-main mt-20">Kostenpflichtig bestellen</button>
                            </div>
                            </div>
                        </form>
                    </div>

                    {{-- RIGHT: Order Summary --}}
                    <div class="col-md-4">
                        <div class="product-checkout-details">
                            <div class="block">
                                <h4 class="widget-title">Bestellübersicht</h4>

                                @forelse($items as $row)
                                    @php
                                        $p = $row['product'];
                                        $thumb = $p?->getFirstMediaUrl('product_main', 'thumb')
                                            ?: $p?->getFirstMediaUrl('product_main')
                                            ?: 'https://via.placeholder.com/80x80';
                                      @endphp
                                    <div class="media product-card">
                                        <a class="pull-left" href="{{ $p ? route('shop.product', $p) : '#' }}">
                                            <img class="media-object" src="{{ $thumb }}" alt="{{ $p?->name ?? 'Artikel' }}"
                                                style="width:64px;height:64px;object-fit:cover;">
                                        </a>
                                        <div class="media-body">
                                            <h4 class="media-heading">
                                                <a href="{{ $p ? route('shop.product', $p) : '#' }}">{{ $p?->name ?? '—' }}</a>
                                            </h4>
                                            <p class="price">{{ $row['qty'] }} × {{ number_format($row['unit'], 2, ',', '.') }}
                                                €</p>

                                            {{-- Optionale Auswahl kurz zeigen --}}
                                            @if(!empty($row['options']))
                                                <div class="text-muted" style="font-size:12px;">
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
                                    <div class="text-muted">Dein Warenkorb ist leer.</div>
                                @endforelse

                                <ul class="summary-prices">
                                    <li>
                                        <span>Zwischensumme (Netto):</span>
                                        <span class="price">{{ number_format($subtotal, 2, ',', '.') }} €</span>
                                    </li>
                                    <li>
                                        <span>MwSt gesamt:</span>
                                        <span class="price">{{ number_format($taxTotal, 2, ',', '.') }} €</span>
                                    </li>
                                </ul>

                                <div class="summary-total">
                                    <span>Gesamt (Brutto)</span>
                                    <span>{{ number_format($grand, 2, ',', '.') }} €</span>
                                </div>

                                <div class="text-muted small" style="margin-top:6px;">
                                    Alle Preise inkl. gesetzl. MwSt.
                                </div>
                            </div>
                        </div>
                    </div>

                </div> {{-- /.row --}}
            </div>
        </div>
    </div>

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

  function show(el, v) { if (el) el.style.display = v ? 'block' : 'none'; }
  function showAlert(msg) { if (!alertBox) return; alertMsg.textContent = msg||'Keine Abholfenster verfügbar.'; show(alertBox, true); }
  function hideAlert() { if (!alertBox) return; alertMsg.textContent = ''; show(alertBox, false); }
  function resetSlots(hideBox=false){ hideAlert(); slots.innerHTML=''; if(hideBox) show(slotsBox, false); }

  function renderOpeningHours(hours) {
    if (!hoursList) return;
    hoursList.innerHTML = '';
    if (!Array.isArray(hours) || hours.length === 0) { show(hoursBox, false); return; }

    hours.forEach(h => {
      const li = document.createElement('li');
      const name = WEEKDAY_NAMES[h.weekday] ?? h.weekday;
      if (h.is_closed) {
        li.textContent = `${name}: geschlossen`;
      } else {
        li.textContent = `${name}: ${h.opens_at?.substring(0,5)} – ${h.closes_at?.substring(0,5)}`;
      }
      hoursList.appendChild(li);
    });
    show(hoursBox, true);
  }

  function setEarliestDate(earliestISO, leadDays) {
    if (!date) return;
    // min setzen (aber min darf nicht in der Vergangenheit liegen)
    date.min = earliestISO;
    if (date.value && date.value < earliestISO) {
      date.value = earliestISO;
    }
    if (leadHint && leadMsg) {
      if ((leadDays ?? 0) > 0) {
        leadMsg.textContent = `Frühestes Abholdatum: ${new Date(earliestISO+'T00:00:00').toLocaleDateString(undefined, { weekday:'short', day:'2-digit', month:'2-digit', year:'numeric' })}.`;
        show(leadHint, true);
      } else {
        show(leadHint, false);
      }
    }
  }

  async function loadMeta() {
    const b = branch.value;
    if (!b) { show(hoursBox, false); show(leadHint, false); return; }

    let resp;
    try {
      resp = await fetch('{{ route('cart.pickup.meta') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ branch_id: b })
      });
    } catch {
      // Meta nicht kritisch – UI unberührt lassen
      return;
    }
    if (!resp.ok) return;

    try {
      const data = await resp.json();
      renderOpeningHours(data.opening_hours);
      setEarliestDate(data.earliest_date, data.lead_days);
    } catch {}
  }

  async function loadSlots() {
    const currentId = ++requestId;
    const b = branch.value;
    const d = date.value;
    if (!b || !d) { resetSlots(true); return; }

    show(slotsBox, true);
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
      const id   = 'slot_' + idx;
      const wrap = document.createElement('div');
      wrap.className = 'radio';

      const label = document.createElement('label');
      label.setAttribute('for', id);

      const input = document.createElement('input');
      input.type  = 'radio';
      input.name  = 'window_start';
      input.id    = id;
      input.value = w.start; // "H:i:s"

      if (!anySelected && oldVal && oldVal === w.start) {
        input.checked = true; anySelected = true;
      }

      label.appendChild(input);
      label.appendChild(document.createTextNode(' ' + (w.label || w.start)));
      wrap.appendChild(label);
      slots.appendChild(wrap);
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
      if (branch.value && date.value) show(slotsBox, true);
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
