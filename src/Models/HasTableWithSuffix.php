<?php

namespace LaraGeoData\Models;

trait HasTableWithSuffix
{
    /**
     * Get base table name (root name, without suffix).
     *
     * @return string
     */
    abstract public function getTableNameRoot(): string;

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        if ($this->table) {
            return $this->table;
        }
        $tableName = $this->getTableNameRoot();
        if ($suffix = config('geonames.database.default_suffix')) {
            $tableName = "{$tableName}_{$suffix}";
        }

        return $tableName;
    }

    /**
     * Initialise model with suffixed table name.
     *
     * @param string|null $suffix
     * @return static
     */
    public static function makeUsingSuffix(?string $suffix = null): static
    {
        $instance  = new static();
        $tableName = $instance->getTableNameRoot();
        if ($suffix) {
            $tableName = "{$tableName}_{$suffix}";
        }

        return $instance->setTable($tableName);
    }
}
