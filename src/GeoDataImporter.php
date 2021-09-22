<?php

namespace LaraGeoData;

use Illuminate\Contracts\Container\Container;
use LaraGeoData\Storage\FilesystemStorage;

class GeoDataImporter
{
    protected FilesystemStorage $storage;

    public function __construct(Container $app)
    {
        $this->storage = new FilesystemStorage($app['files'], $app['config']->get('geonames.storage.path'));
    }

    public function storeFileFromUrl(string $url, ?string $name, ?\Closure $progressCallback = null, bool $force = false): bool
    {
        if (!($exists = $this->storage->exists($name)) || $force) {
            if ($exists) {
                $this->storage->delete($name);
            }

            return $this->storage->storeFromUrl($url, $name, $progressCallback);
        }

        return false;
    }

    /**
     * @param string $file
     *
     * @return bool
     * @throws \Exception
     */
    public function extractFile(string $file): bool
    {
        return $this->storage->extractZipFile($file);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function truncateStorage(): bool
    {
        return $this->storage->truncate();
    }
}
