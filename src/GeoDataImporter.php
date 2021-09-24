<?php

namespace LaraGeoData;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use LaraGeoData\Storage\FilesystemStorage;

/**
 * @method bool storageCreateFromUrl(string $url, ?string $name, ?\Closure $progressCallback = null, bool $force = false)
 * @method bool storageExtractZipFile(string $file)
 * @method bool storagePath(string $file)
 * @method bool storageTruncate()
 */
class GeoDataImporter
{
    protected FilesystemStorage $storage;

    public function __construct(Container $app)
    {
        $this->storage = new FilesystemStorage($app['files'], $app['config']->get('geonames.storage.path'));
    }

    /**
     * Dynamically forward call.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'storage')) {
            $realMethodName = lcfirst(Str::after($method, 'storage'));
            if (method_exists($this->storage, $realMethodName)) {
                return $this->storage->{$realMethodName}(...$parameters);
            }
        }

        throw new \BadMethodCallException("Method [{$method}] not exists.");
    }

    /**
     * Get current storage instance.
     *
     * @return FilesystemStorage
     */
    public function storage(): FilesystemStorage
    {
        return $this->storage;
    }
}
