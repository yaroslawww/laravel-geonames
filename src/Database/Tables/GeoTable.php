<?php

namespace LaraGeoData\Database\Tables;

interface GeoTable
{

    /**
     * Create migration from template.
     *
     * @param string|null $suffix
     * @return string
     */
    public function makeMigration(?string $suffix = null): string;

    /**
     * Load data from csv to table.
     *
     * @param string|null $filePath
     * @param string|null $suffix
     * @param bool        $truncate
     * @throws \Exception
     */
    public function loadData(?string $filePath = null, ?string $suffix = null,  $truncate = true);
}
