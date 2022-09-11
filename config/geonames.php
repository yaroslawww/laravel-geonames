<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Download repositories links.
    |--------------------------------------------------------------------------
    |
    | See http://geonames.org
    |
    */
    'import_repos' => [
        'general' => env('GEONAMES_IMPORT_REPO', 'http://download.geonames.org/export/dump'),
        'zip'     => env('GEONAMES_IMPORT_POSTAL_CODE_REPO', 'http://download.geonames.org/export/zip'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Local file storage configuration.
    |--------------------------------------------------------------------------
    |
    */
    'storage' => [
        'path'              => storage_path('geonames'),
        'postal_codes_dir'  => 'postal_codes',
        'download_provider' => 'wget', // 'curl_php'
    ],

    /*
    |--------------------------------------------------------------------------
    | Import database configuration.
    |--------------------------------------------------------------------------
    |
    */
    'database' => [
        'connection' => env('GEONAMES_DB_CONNECTION', env('DB_CONNECTION', 'mysql_geo')),

        /** Used only in model */
        'default_suffix' => env('GEONAMES_DEFAULT_SUFFIX'),

        'tables' => [
            'geo_feature_classes' => 'geo_feature_classes',
            'geo_feature_codes'   => 'geo_feature_codes',
            'geonames'            => 'geonames',
            'postalcodes'         => 'geo_postal_codes',
        ],
    ],
];
