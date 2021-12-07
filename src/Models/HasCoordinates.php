<?php

namespace LaraGeoData\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use LaraGeoData\Contracts\ModelWithCoordinates;

trait HasCoordinates
{
    /**
     * Table column name for latitude.
     */
    public function latitudeColName(): string
    {
        return $this->latitudeColumnName ?? 'lat';
    }

    /**
     * Table column name for longitude.
     */
    public function longitudeColName(): string
    {
        return $this->longitudeColumnName ?? 'lng';
    }

    /**
     * Filed name for distance. This field will be created only id use "nearest" scope.
     */
    public function distanceColName(): string
    {
        return $this->distanceColumnName ?? 'distance';
    }

    /**
     * Haversine formula (from Google solution example).
     * By default use radius in kilometers.
     */
    public function scopeNearest(Builder $query, float $lat, float $lng, float $radius, int $coef = ModelWithCoordinates::HAVERSINE_COEF_KILOMETERS)
    {
        $latColName        = $this->latitudeColName();
        $lngColName        = $this->longitudeColName();
        $distanceFieldName = $this->distanceColName();

        return $query->select([
            '*',
            DB::raw("
               ( {$coef} *
               acos(cos(radians({$lat})) *
               cos(radians({$latColName})) *
               cos(radians({$lngColName}) -
               radians({$lng})) +
               sin(radians({$lat})) *
               sin(radians({$latColName})))
            ) AS {$distanceFieldName} "),
        ])->having($distanceFieldName, '<=', $radius);
    }

    public function scopeNearestInKilometers(Builder $query, float $lat, float $lng, float $radius)
    {
        return $this->scopeNearest($query, $lat, $lng, $radius, ModelWithCoordinates::HAVERSINE_COEF_KILOMETERS);
    }

    public function scopeNearestInMiles(Builder $query, float $lat, float $lng, float $radius)
    {
        return $this->scopeNearest($query, $lat, $lng, $radius, ModelWithCoordinates::HAVERSINE_COEF_MILES);
    }

    /**
     * Order query results by distance.
     */
    public function scopeOrderByNearest(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy($this->distanceColName(), $direction);
    }

    /**
     * Field has value only is use "nearest" scope.
     */
    public function getDistanceAttribute(): float
    {
        if (array_key_exists($this->distanceColName(), $this->attributes)) {
            return (float) $this->attributes[$this->distanceColName()];
        }

        return 0;
    }

    public function getLatitude(): ?float
    {
        return $this->{$this->latitudeColName()};
    }

    public function getLongitude(): ?float
    {
        return $this->{$this->longitudeColName()};
    }

    public function getDistance(): float
    {
        return $this->distance;
    }
}
