<?php

namespace LaraGeoData\Tests;

use PDO;

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

        $options                                 = $app['config']->get('database.connections.mysql_geo.options') ?? [];
        $options[ PDO::MYSQL_ATTR_LOCAL_INFILE ] = true;
        $app['config']->set('database.connections.mysql_geo.options', $options);

        // $app['config']->set('geonames.some-key', 'some-val');
    }
}
