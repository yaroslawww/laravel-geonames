<?php

namespace LaraGeoData\Console\Commands;

use LaraGeoData\Database\Tables\AdminAreasView;
use LaraGeoData\Database\Tables\Geonames;
use LaraGeoData\Database\Tables\GeoTable;
use LaraGeoData\Database\Tables\Postalcodes;

trait HasTablesClassesMap
{
    /**
     * Map describe command type and table class.
     *
     * @var array
     */
    protected array $classesMap = [
        'geonames'         => Geonames::class,
        'postalcodes'      => Postalcodes::class,
        'admin_areas_view' => AdminAreasView::class,
    ];

    /**
     * @param string $type
     * @return GeoTable
     * @throws \Exception
     */
    public function getTableClassNameByType(string $type)
    {
        if (!isset($this->classesMap[$type]) || !is_a($this->classesMap[$type], GeoTable::class, true)) {
            throw new \Exception("Class for type [{$type}] not found.");
        }

        return $this->classesMap[$type];
    }

    /**
     * @param string $type
     * @param        ...$attributes
     * @return mixed
     * @throws \Exception
     */
    public function makeTableObjectNameByType(string $type, ...$attributes): GeoTable
    {
        $tableClassName = $this->getTableClassNameByType($type);

        /** @psalm-suppress UndefinedClass */
        return new $tableClassName(...$attributes);
    }
}
