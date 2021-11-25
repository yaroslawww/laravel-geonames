<?php

namespace LaraGeoData\Models;

use Illuminate\Database\Eloquent\Model;

class Geoname extends Model
{
    use HasTableWithSuffix, HasCoordinates;

    protected $primaryKey = 'geoname_id';
    public $incrementing  = false;

    /**
     * @inheritDoc
     */
    public function getTableNameRoot(): string
    {
        return config('geonames.database.tables.geonames');
    }
}
