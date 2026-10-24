<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Master Cryptographic Salt
    |--------------------------------------------------------------------------
    | High-entropy private secret used to sign and verify DineFlow AI licenses.
    | Do NOT share this secret with clients or buyers.
    */
    'master_secret' => env('LICENSE_MASTER_SECRET', 'df_master_sec_99a838b_shubh_patel_dineflow_2026_x!q9'),

    /*
    |--------------------------------------------------------------------------
    | License Storage Path
    |--------------------------------------------------------------------------
    | File location where the active cryptographic license token is stored.
    */
    'license_file' => storage_path('license.key'),

    /*
    |--------------------------------------------------------------------------
    | Local Development Bypass
    |--------------------------------------------------------------------------
    | Allows localhost and 127.0.0.1 to boot during local development.
    */
    'allow_local_dev' => env('LICENSE_ALLOW_LOCAL', true),

    /*
    |--------------------------------------------------------------------------
    | System Rights Owner & Contact
    |--------------------------------------------------------------------------
    */
    'owner' => 'Shubh Patel',
    'contact_phone' => '+91 77779 38338',
    'contact_email' => 'shubhmpatel77@gmail.com',
    'support_url' => 'https://wa.me/917777938338',
];
