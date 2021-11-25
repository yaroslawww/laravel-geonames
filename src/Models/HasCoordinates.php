<?php

namespace LaraGeoData\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasCoordinates
{
    public function latitudeColName(): string
    {
        return $this->latitudeColumnName ?? 'lat';
    }

    public function longitudeColName(): string
    {
        return $this->longitudeColumnName ?? 'lng';
    }

    public function distanceColName(): string
    {
        return $this->distanceColumnName ?? 'distance';
    }

    /**
     * Haversine formula (from Google solution example)
     *
     * @param Builder $query
     * @param float   $lat
     * @param float   $lng
     * @param float   $radius
     * @param int     $koef miles = 3959, kilometers = 6371
     * @return Builder
     */
    public function scopeNearest(Builder $query, float $lat, float $lng, float $radius, int $koef = 6371)
    {
        $latColName        = $this->latitudeColName();
        $lngColName        = $this->longitudeColName();
        $distanceFieldName = $this->distanceColName();

        return $query->select([
            '*',
            DB::raw("
               ( {$koef} *
               acos(cos(radians({$lat})) *
               cos(radians({$latColName})) *
               cos(radians({$lngColName}) -
               radians({$lng})) +
               sin(radians({$lat})) *
               sin(radians({$latColName})))
            ) AS {$distanceFieldName} "),
        ])->having($distanceFieldName, '<=', $radius);
    }

    public function scopeNearestInMiles(Builder $query, float $lat, float $lng, float $radius, int $koef = 3959)
    {
        return $this->scopeNearest($query, $lat, $lng, $radius, 3959);
    }

    public function scopeOrderByNearest(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy($this->distanceColName(), $direction);
    }

    public function getDistanceAttribute(): float
    {
        if (array_key_exists($this->distanceColName(), $this->attributes)) {
            return (float) $this->attributes[$this->distanceColName()];
        }

        return 0;
    }
}
