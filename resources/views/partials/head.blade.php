@php
    $siteName = (isset($siteName) ? $siteName : null) ?? \App\Models\Setting::where('key', 'site_name')->value('value') ?? config('app.name');
    $logoImage = (isset($logoImage) ? $logoImage : null) ?? \App\Models\Setting::where('key', 'logo_image')->value('value');
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? $siteName }}</title>

@if($logoImage)
    <link rel="icon" href="{{ Storage::url($logoImage) }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ Storage::url($logoImage) }}">
    <meta property="og:image" content="{{ url(Storage::url($logoImage)) }}">
@else
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
@endif

<meta property="og:title" content="{{ $title ?? $siteName }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
