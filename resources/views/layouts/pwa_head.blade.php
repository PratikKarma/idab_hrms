@php
    $settings = \Illuminate\Support\Facades\DB::table('settings')
        ->whereIn('name', [
            'pwa_name',
            'pwa_short_name',
            'pwa_description',
            'pwa_theme_color',
            'pwa_background_color',
        ])
        ->pluck('value', 'name')
        ->toArray();

    $name = $settings['pwa_name'] ?? 'IDAB TECH - (HRMS)';
    $shortName = $settings['pwa_short_name'] ?? 'HRMS';
    $description = $settings['pwa_description'] ?? 'A Progressive Web Application setup for Laravel projects.';
    $themeColor = $settings['pwa_theme_color'] ?? '#6777ef';
    $backgroundColor = $settings['pwa_background_color'] ?? '#6777ef';
@endphp

<!-- PWA Meta -->
<meta name="theme-color" content="{{ $themeColor }}"/>
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="application-name" content="{{ $shortName }}">
<meta name="apple-mobile-web-app-title" content="{{ $shortName }}">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="description" content="{{ $description }}">

<link rel="manifest" href="{{ app()->environment('local') ? asset('manifest.json') : asset('public/manifest.json') }}">
<link rel="apple-touch-icon" href="{{ asset(app()->environment('local') ? 'logo.png' : 'public/logo.png') }}">

<!-- iOS splash screen fallback -->
<link rel="icon" sizes="192x192" href="{{ asset(app()->environment('local') ? 'logo.png' : 'public/logo.png') }}">
