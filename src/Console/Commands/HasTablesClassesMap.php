<?php

namespace LaraGeoData\Console\Commands;

use LaraGeoData\Database\Tables\Geonames;
use LaraGeoData\Database\Tables\GeoTable;
use LaraGeoData\Database\Tables\Postalcodes;

trait HasTablesClassesMap
{

    /**
     * @var array
     */
    protected array $classesMap = [
        'geonames'    => Geonames::class,
        'postalcodes' => Postalcodes::class,
    ];

    public function getTableClassNameByType(string $type)
    {
        if (!isset($this->classesMap[$type]) || !is_a($this->classesMap[$type], GeoTable::class, true)) {
            throw new \Exception("Class for type [{$type}] not found.");
        }

        return $this->classesMap[$type];
    }
}
