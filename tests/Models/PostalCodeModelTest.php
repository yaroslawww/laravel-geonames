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

        $model = PostalCode::useSuffix('foo');
        $this->assertEquals($model->getTableNameRoot() . '_foo', $model->getTable());

        Config::set('geonames.database.default_suffix', 'bar');
        $model = new PostalCode();
        $this->assertEquals($model->getTableNameRoot() . '_bar', $model->getTable());
        Config::set('geonames.database.default_suffix', null);
        $model = new PostalCode();
        $this->assertEquals($model->getTableNameRoot(), $model->getTable());
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

        $model = PostalCode::useSuffix('ar');

        /** @var Builder $query */
        $query = $model->newQuery()->nearestInMiles(-29.9333, -58.2667, 0.1)->orderByNearest();
        $this->assertEquals(1, $query->count());
        $this->assertEquals('PAGO LARGO', $query->first()->place_name);
    }
}
