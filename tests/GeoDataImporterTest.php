<?php

namespace LaraGeoData\Tests;

use LaraGeoData\Facades\GeoDataImporter;
use LaraGeoData\Storage\FilesystemStorage;

class GeoDataImporterTest extends TestCase
{

    /** @test */
    public function exception_if_not_valid_method()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method [blaBla] not exists.');
        GeoDataImporter::blaBla();
    }

    /** @test */
    public function exception_if_not_valid_storage_method()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method [storageSomeMethod] not exists.');
        GeoDataImporter::storageSomeMethod();
    }

    /** @test */
    public function get_storage()
    {
        $this->assertInstanceOf(FilesystemStorage::class, GeoDataImporter::storage());
    }
}
