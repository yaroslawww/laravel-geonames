<?php

namespace LaraGeoData\Tests;

use Illuminate\Support\Facades\DB;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \LaraGeoData\ServiceProvider::class,
        ];
    }

    public function defineEnvironment($app)
    {
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('database.default', 'mysql_geo');

        if (!env('SCRUTINIZER')) {
            $app['config']->set(
                'database.connections.mysql_geo',
                array_merge(
                    $app['config']->get('database.connections.mysql'),
                    include __DIR__ . '/db_config.php'
                )
            );
        } else {
            $app['config']->set(
                'database.connections.mysql_geo',
                $app['config']->get('database.connections.mysql')
            );
        }

        $connection = $app['config']->get('database.default');
        $schemaName = $app['config']->get("database.connections.{$connection}.database");
        $charset    = $app['config']->get("database.connections.{$connection}.charset", 'utf8mb4');
        $collation  = $app['config']->get("database.connections.{$connection}.collation", 'utf8mb4_unicode_ci');

        $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";
        DB::statement($query);


        // $app['config']->set('geonames.some-key', 'some-val');
    }
}
