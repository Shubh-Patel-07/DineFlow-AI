<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DineFlow Platform Branding & Marketing Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized platform-level configuration for SaaS attribution, marketing
    | links (Instagram @smp.officials), and platform branding.
    |
    */

    'platform_name' => 'DineFlow',
    'platform_tagline' => 'Restaurant Suite v2.0',
    'logo' => '/images/dineflow_logo.png',
    'logo_alt' => '/images/dineflow_logo.jpg',

    'instagram' => [
        'handle' => env('DINEFLOW_INSTAGRAM_HANDLE', '@smp.officials'),
        'url' => env('DINEFLOW_INSTAGRAM_URL', 'https://www.instagram.com/smp.officials/'),
    ],

    'smp' => [
        // Note: SMP ASSET NOT FOUND in physical assets; using verified typographic pill
        'pill_text' => 'SMP',
        'asset' => null,
    ],
];