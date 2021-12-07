<?php

namespace LaraGeoData\Models;

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
}
