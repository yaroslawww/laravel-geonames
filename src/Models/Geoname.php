<?php

namespace LaraGeoData\Models;

use Illuminate\Database\Eloquent\Model;

class Geoname extends Model
{
    use HasTableWithSuffix, HasCoordinates, HasLocationName;

    protected $primaryKey = 'geoname_id';
    public $incrementing  = false;

    protected $guarded = [];

    protected string $locationNameColumn = 'ascii_name';

    /**
     * @inheritDoc
     */
    public function getTableNameRoot(): string
    {
        return config('geonames.database.tables.geonames');
    }
}
