<?php
// app/Providers/ViewServiceProvider.php

namespace App\Providers;

use App\Models\BrandSetting;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer(['*'], function ($view) {
            // Branding
            $brand = BrandSetting::cached();

            $logoUrl    = $brand->getFirstMediaUrl('logo', 'web') ?: $brand->getFirstMediaUrl('logo');
            $favicon32  = $brand->getFirstMediaUrl('favicon', '32') ?: $brand->getFirstMediaUrl('favicon');
            $favicon180 = $brand->getFirstMediaUrl('favicon', '180') ?: null;

            // SEO Defaults
            $seo = SeoSetting::cached();
            $ogImage = $seo->getFirstMediaUrl('og_image') ?: null;

            $view->with([
                // Branding
                'branding'   => $brand,
                'brandLogo'  => $logoUrl,
                'favicon32'  => $favicon32,
                'favicon180' => $favicon180,

                'seoDefaults' => $seo,
                'seoOgImage'  => $ogImage,
            ]);
        });
    }
}
