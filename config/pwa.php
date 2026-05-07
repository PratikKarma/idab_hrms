<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Would you like the install button to appear on all pages?
      Set true/false
    |--------------------------------------------------------------------------
    */

    'install-button' => true,

    /*
    |--------------------------------------------------------------------------
    | PWA Manifest Configuration
    |--------------------------------------------------------------------------
    |  php artisan erag:pwa-update-manifest
    */

    'manifest' => function () {
        $default = [
            'name' => 'IDAB TECH - (HRMS)',
            'short_name' => 'HRMS',
            'background_color' => '#6777ef',
            'display' => 'fullscreen',
            'description' => 'A Progressive Web Application setup for Laravel projects.',
            'theme_color' => '#6777ef',
            'icons' => [
                [
                    'src' => 'logo.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
        ];

        try {
            $stored = \Illuminate\Support\Facades\DB::table('settings')
                ->whereIn('name', [
                    'pwa_name',
                    'pwa_short_name',
                    'pwa_background_color',
                    'pwa_display',
                    'pwa_description',
                    'pwa_theme_color',
                    'pwa_icon',
                ])
                ->pluck('value', 'name')
                ->toArray();

            // Map the DB keys to manifest keys
            $prefix = app()->environment('local') ? '' : 'public/';

            return [
                'name' => $stored['pwa_name'] ?? $default['name'],
                'short_name' => $stored['pwa_short_name'] ?? $default['short_name'],
                'background_color' => $stored['pwa_background_color'] ?? $default['background_color'],
                'display' => $stored['pwa_display'] ?? $default['display'],
                'description' => $stored['pwa_description'] ?? $default['description'],
                'theme_color' => $stored['pwa_theme_color'] ?? $default['theme_color'],
                'icons' => [
                    [
                        'src' => isset($stored['pwa_icon']) ? $prefix . 'storage/' . $stored['pwa_icon'] : $prefix . $default['icons'][0]['src'],
                        'sizes' => '512x512',
                        'type' => 'image/png',
                    ],
                ],
            ];
        } catch (\Exception $e) {
            return $default;
        }
    },

    /*
    |--------------------------------------------------------------------------
    | Debug Configuration
    |--------------------------------------------------------------------------
    | Toggles the application's debug mode based on the environment variable
    */

    'debug' => env('APP_DEBUG', false),

];
