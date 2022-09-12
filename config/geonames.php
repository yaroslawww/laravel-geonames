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

        /**
         * Used only in model.
         * Usefull, in case if you use only one country in your application.
         */
        'default_suffix' => env('GEONAMES_DEFAULT_SUFFIX'),

        'tables' => [
            'geonames'            => 'gn_geonames',
            'postalcodes'         => 'gn_postal_codes',
            'admin_areas'         => 'gn_admin{level}_areas_view',
        ],
    ],
];
