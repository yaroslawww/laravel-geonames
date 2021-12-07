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

        $model = Geoname::makeUsingSuffix('foo');
        $this->assertEquals($model->getTableNameRoot() . '_foo', $model->getTable());

        Config::set('geonames.database.default_suffix', 'bar');
        $model = new Geoname();
        $this->assertEquals($model->getTableNameRoot() . '_bar', $model->getTable());

        Config::set('geonames.database.default_suffix', null);
        $model = new Geoname();
        $this->assertEquals($model->getTableNameRoot(), $model->getTable());

        $model = new Geoname([
            'lat'        => 12,
            'lng'        => 13,
            'distance'   => 14,
            'ascii_name' => 'Test name',
        ]);

        $this->assertEquals(12, $model->getLatitude());
        $this->assertEquals(13, $model->getLongitude());
        $this->assertEquals(14, $model->getDistance());
        $this->assertEquals('Test name', $model->locationName());
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

        $model = Geoname::makeUsingSuffix('ae');

        /** @var Builder $query */
        $query = $model->newQuery()->nearestInMiles(24.76778, 56.16306, 0)->orderByNearest();
        $this->assertEquals(1, $query->count());
        $this->assertEquals('Wad Gharr', $query->first()->ascii_name);
        $this->assertEquals(0, round($query->first()->distance, 2));
    }
}
