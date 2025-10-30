<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>{{ $metaTitle ?? $seoDefaults->default_meta_title ?? 'Stadtbäckerei Kühl' }}</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ $metaDescription ?? $seoDefaults->default_meta_description ?? '' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-name" content="aviato" />

    {{-- Dynamisches Favicon --}}
    @if(!empty($favicon32))
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon32 }}">
    @endif
    @if(!empty($favicon180))
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $favicon180 }}">
    @endif

    {{-- OG / Social --}}
    @if(!empty($seoOgImage))
        <meta property="og:image" content="{{ $seoOgImage }}">
    @endif
    @if(!empty($seoDefaults->meta_tags))
        @foreach($seoDefaults->meta_tags as $name => $value)
            <meta name="{{ $name }}" content="{{ $value }}">
        @endforeach
    @endif

    @vite('resources/css/app.css')
</head>

<body id="body">


    @includeIf('partials.header')

    @yield('content')

    @includeIf('partials.footer')



    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>

</html>