<?php

namespace LaraGeoData\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use LaraGeoData\Models\Geoname;
use LaraGeoData\Tests\TestCase;

class GeonameModelTest extends TestCase
{

    /** @test */
    public function dynamic_table_name()
    {
        $model = new Geoname();
        $this->assertEquals($model->getTableNameRoot(), $model->getTable());

        $model = Geoname::useSuffix('foo');
        $this->assertEquals($model->getTableNameRoot() . '_foo', $model->getTable());

        Config::set('geonames.database.default_suffix', 'bar');
        $model = new Geoname();
        $this->assertEquals($model->getTableNameRoot() . '_bar', $model->getTable());
        Config::set('geonames.database.default_suffix', null);
        $model = new Geoname();
        $this->assertEquals($model->getTableNameRoot(), $model->getTable());
    }

    /** @test */
    public function nearest_position_search()
    {
        $this->artisan('geonames:download', [
            'files'     => ['AE.zip',],
            '--force'   => true,
            '--extract' => true,
        ]);
        $this->artisan('geonames:make:migration geonames --suffix=ae');
        $this->artisan('migrate:fresh');
        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'geonames',
            '--suffix' => 'ae',
        ]);

        $model = Geoname::useSuffix('ae');

        /** @var Builder $query */
        $query = $model->newQuery()->nearestInMiles(24.76778, 56.16306, 0)->orderByNearest();
        $this->assertEquals(1, $query->count());
        $this->assertEquals('Wad Gharr', $query->first()->ascii_name);
        $this->assertEquals(0, $query->first()->distance);
    }
}
