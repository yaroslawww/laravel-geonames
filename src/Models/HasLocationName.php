<?php

namespace LaraGeoData\Models;

use Illuminate\Database\Eloquent\Builder;

trait HasLocationName
{
    /**
     * Filed name for location name.
     */
    public function locationNameColumn(): string
    {
        return $this->locationNameColumn ?? 'location_name';
    }

    public function locationName(): ?string
    {
        return $this->{$this->locationNameColumn()};
    }

    public function scopeOrderByName(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy($this->locationNameColumn(), $direction);
    }

    public function scopeNameContain(Builder $query, string $value)
    {
        return $query->where($this->locationNameColumn(), 'like', "%$value%");
    }

    public function scopeNameStartsWith(Builder $query, string $value)
    {
        return $query->where($this->locationNameColumn(), 'like', "$value%");
    }

    public function scopeNameEndsWith(Builder $query, string $value)
    {
        return $query->where($this->locationNameColumn(), 'like', "$value%");
    }
}
