<?php

namespace LaraGeoData\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasTableWithSuffix, HasCoordinates;

    protected $primaryKey = 'country_code';
    public $incrementing  = false;

    public function getTableNameRoot(): string
    {
        return config('geonames.database.tables.postalcodes');
    }
}
