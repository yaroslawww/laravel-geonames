<?php

namespace LaraGeoData;

use LaraGeoData\Console\Commands\DownloadFilesCommand;
use LaraGeoData\Console\Commands\DownloadTruncateCommand;
use LaraGeoData\Console\Commands\MakeMigrationCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/geonames.php' => config_path('geonames.php'),
            ], 'config');


            $this->commands([
                DownloadFilesCommand::class,
                DownloadTruncateCommand::class,
                MakeMigrationCommand::class,
            ]);
        }

        $this->app->bind('geodata-importer', function ($app) {
            return new GeoDataImporter($app);
        });
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/geonames.php', 'geonames');
    }
}
