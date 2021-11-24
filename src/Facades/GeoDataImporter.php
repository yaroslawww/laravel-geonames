<?php

namespace LaraGeoData\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool storageCreateFromUrl(string $url, ?string $name, ?\Closure $progressCallback = null, bool $force = false)
 * @method static bool storageExtractZipFile(string $file)
 * @method static bool storagePath(string $file = '')
 * @method static bool storageTruncate()
 */
class GeoDataImporter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'geodata-importer';
    }
}
