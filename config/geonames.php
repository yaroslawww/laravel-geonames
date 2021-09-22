<?php

return [

    'import_repo'             => env('GEONAMES_IMPORT_REPO', 'http://download.geonames.org/export/dump'),
    'import_postal_code_repo' => env('GEONAMES_IMPORT_POSTAL_CODE_REPO', 'http://download.geonames.org/export/zip'),

    'storage' => [
        'path'              => storage_path('geonames'),
        'postal_codes_dir'  => 'postal_codes',
        'download_provider' => 'wget', // 'curl_php'
    ],
];
