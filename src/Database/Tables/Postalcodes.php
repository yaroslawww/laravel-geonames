<?php

namespace LaraGeoData\Database\Tables;

class Postalcodes extends Table
{
    protected $defaultDataFilePathPrefix = 'postal_codes/';

    public function getTemplateNameRoot(): string
    {
        return 'create_geo_postal_codes_table';
    }

    public function getTableNameRoot(): string
    {
        return config('geonames.database.tables.postalcodes');
    }

    protected function orderedColumnsListToLoad(): array
    {
        return [
            'country_code',
            'postal_code',
            'place_name',
            'admin_name1',
            'admin_code1',
            'admin_name2',
            'admin_code2',
            'admin_name3',
            'admin_code3',
            'lat',
            'lng',
            'accuracy',
        ];
    }
}
