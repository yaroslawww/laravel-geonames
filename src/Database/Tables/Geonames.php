<?php

namespace LaraGeoData\Database\Tables;

class Geonames extends Table
{
    public function getTemplateNameRoot(): string
    {
        return 'create_geonames_table';
    }

    public function getTableNameRoot(): string
    {
        return config('geonames.database.tables.geonames');
    }

    protected function orderedColumnsListToLoad(): array
    {
        return [
            'geoname_id',
            'name',
            'ascii_name',
            'alternate_names',
            'lat',
            'lng',
            'fclass',
            'fcode',
            'country',
            'cc2',
            'admin1',
            'admin2',
            'admin3',
            'admin4',
            'population',
            'elevation',
            'gtopo30',
            'timezone',
            'moddate',
        ];
    }
}
