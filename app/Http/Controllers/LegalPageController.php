<?php

// app/Http/Controllers/LegalPageController.php

namespace App\Http\Controllers;

use App\Models\LegalSetting;
use App\Models\LegalSettings;

class LegalPageController extends Controller
{
    protected function data(): array
    {
        $legal = LegalSetting::cached();

        // Robust gegenüber leicht unterschiedlicher Feldnamen (“…_html”, “content”, o.Ä.)
        $pick = fn(...$keys) => collect($keys)->map(fn($k) => data_get($legal, $k))->first(fn($v) => filled($v));

        return compact('legal', 'pick');
    }

    public function impressum()
    {
        ['legal' => $legal, 'pick' => $pick] = $this->data();

        return view('legal.impressum', [
            'title'   => $legal->impressum_title ?? 'Impressum',
            'content' => $pick('impressum_html', 'impressum_content', 'impressum'),
        ]);
    }

    public function datenschutz()
    {
        ['legal' => $legal, 'pick' => $pick] = $this->data();

        return view('legal.datenschutz', [
            'title'   => $legal->datenschutz_title ?? 'Datenschutzerklärung',
            'content' => $pick('datenschutz_html', 'datenschutz_content', 'datenschutz'),
        ]);
    }

    public function agb()
    {
        ['legal' => $legal, 'pick' => $pick] = $this->data();

        return view('legal.agb', [
            'title'   => $legal->agb_title ?? 'AGB',
            'content' => $pick('agb_html', 'agb_content', 'agb'),
        ]);
    }

    public function widerruf()
    {
        ['legal' => $legal, 'pick' => $pick] = $this->data();

        abort_unless(
            filled($pick('widerruf_html', 'widerruf_content', 'widerruf')),
            404
        );

        return view('legal.widerruf', [
            'title'   => $legal->widerruf_title ?? 'Widerruf',
            'content' => $pick('widerruf_html', 'widerruf_content', 'widerruf'),
        ]);
    }
}
