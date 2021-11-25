<?php

namespace LaraGeoData\Tests\Console;

use LaraGeoData\Tests\TestCase;

class LoadDataToDBCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh')->assertExitCode(0);
    }

    /** @test */
    public function error_if_type_not_exists()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Class for type [geodata] not found.');

        $this->artisan('geonames:import:file-to-db', [
            'type' => 'geodata',
            'file' => storage_path('myFileRandom.csv'),
        ]);
    }

    /** @test */
    public function error_if_file_not_exists()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File [' . storage_path('myFileRandom.csv') . '] not found.');

        $this->artisan('geonames:import:file-to-db', [
            'type' => 'geonames',
            'file' => storage_path('myFileRandom.csv'),
        ]);
    }

    /** @test */
    public function error_if_table_not_exists()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Table [geonames_not_table] not found. Maybe you need run migrations.');

        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'geonames',
            '--suffix' => 'not_table',
        ]);
    }
}
