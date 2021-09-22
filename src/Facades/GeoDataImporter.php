<?php

namespace LaraGeoData\Facades;

use Illuminate\Support\Facades\Facade;

class GeoDataImporter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'geodata-importer';
    }
}
