<?php

namespace LaraGeoData\Models;

trait HasTableWithSuffix
{
    abstract public function getTableNameRoot(): string;

    public function getTable()
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

    public static function useSuffix(?string $suffix = null): static
    {
        $instance  = new static();
        $tableName = $instance->getTableNameRoot();
        if ($suffix) {
            $tableName = "{$tableName}_{$suffix}";
        }

        return $instance->setTable($tableName);
    }
}
