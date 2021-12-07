<?php

namespace LaraGeoData\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasTableWithSuffix, HasCoordinates, HasLocationName;

    protected $primaryKey = 'postal_code';
    public $incrementing  = false;

    protected $guarded = [];

    protected string $locationNameColumn = 'postal_code';

    /**
     * @inheritDoc
     */
    public function getTableNameRoot(): string
    {
        return config('geonames.database.tables.postalcodes');
    }
}
