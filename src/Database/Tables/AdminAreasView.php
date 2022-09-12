<?php

namespace LaraGeoData\Database\Tables;

class AdminAreasView extends Table
{
    public function getTemplateNameRoot(): string
    {
        return 'create_geo_admin_areas_table';
    }

    public function getTableNameRoot(): string
    {
        return config('geonames.database.tables.admin_areas');
    }

    protected function orderedColumnsListToLoad(): array
    {
        return [];
    }
}
