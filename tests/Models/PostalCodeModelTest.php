<?php

namespace LaraGeoData\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use LaraGeoData\Models\PostalCode;
use LaraGeoData\Tests\TestCase;

class PostalCodeModelTest extends TestCase
{

    /** @test */
    public function dynamic_table_name()
    {
        $model = new PostalCode();
        $this->assertEquals($model->getTableNameRoot(), $model->getTable());

        $model = PostalCode::makeUsingSuffix('foo');
        $this->assertEquals($model->getTableNameRoot() . '_foo', $model->getTable());

        Config::set('geonames.database.default_suffix', 'bar');
        $model = new PostalCode();
        $this->assertEquals($model->getTableNameRoot() . '_bar', $model->getTable());

        Config::set('geonames.database.default_suffix', null);
        $model = new PostalCode();
        $this->assertEquals($model->getTableNameRoot(), $model->getTable());

        $model = new PostalCode([
            'lat'         => 12,
            'lng'         => 13,
            'distance'    => 14,
            'postal_code' => 'Test postal name',
        ]);

        $this->assertEquals(12, $model->getLatitude());
        $this->assertEquals(13, $model->getLongitude());
        $this->assertEquals(14, $model->getDistance());
        $this->assertEquals('Test postal name', $model->locationName());
    }

    /** @test */
    public function nearest_position_search()
    {
        $this->artisan('geonames:download', [
            '--postal'  => ['AR.zip',],
            '--force'   => true,
            '--extract' => true,
        ]);
        $this->artisan('geonames:make:migration postalcodes --suffix=ar');
        $this->artisan('migrate:fresh');
        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'postalcodes',
            '--suffix' => 'ar',
        ]);

        $model = PostalCode::makeUsingSuffix('ar');

        /** @var Builder $query */
        $query = $model->newQuery()->nearestInKilometers(-29.9333, -58.2667, 0.1)->orderByNearest();
        $this->assertEquals(1, $query->count());
        $this->assertEquals('PAGO LARGO', $query->first()->place_name);
        $this->assertEquals(0, round($query->first()->distance, 2));
    }
}
