<?php

namespace LaraGeoData\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use LaraGeoData\Database\MigrationHelper;

class MigrationHelperTest extends TestCase
{
    /** @test */
    public function geonames_columns()
    {
        $tableMock  = $this->mock(Blueprint::class);
        $columnMock = $this->mock(ColumnDefinition::class);

        $columnMock->shouldReceive('default')
                   ->times(1)->andReturnSelf();
        $columnMock->shouldReceive('nullable')
                   ->times(18)->andReturnSelf();
        $columnMock->shouldReceive('index')
                   ->times(17)->andReturnSelf();
        $tableMock->shouldReceive('primary')
                  ->once();

        $tableMock->shouldReceive('unsignedInteger')
                  ->twice()->andReturn($columnMock);
        $tableMock->shouldReceive('string')
                  ->times()->andReturn($columnMock);
        $tableMock->shouldReceive('text')
                  ->once()->andReturn($columnMock);
        $tableMock->shouldReceive('integer')
                  ->twice()->andReturn($columnMock);
        $tableMock->shouldReceive('decimal')
                  ->twice()->andReturn($columnMock);
        $tableMock->shouldReceive('dateTime')
                  ->once()->andReturn($columnMock);
        $tableMock->shouldReceive('char')
                  ->once()->andReturn($columnMock);
        $tableMock->shouldReceive('unsignedSmallInteger')
                  ->once()->andReturn($columnMock);
        $tableMock->shouldReceive('nullableTimestamps')
                  ->once();




        MigrationHelper::geonamesDefaultColumns($tableMock);
    }
}
