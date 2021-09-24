<?php

namespace LaraGeoData\Tests\Console;

use Illuminate\Support\Facades\DB;
use LaraGeoData\Facades\GeoDataImporter;
use LaraGeoData\Tests\TestCase;

class LoadDataToDBCommandGeonamesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh')->assertExitCode(0);
    }

    /** @test */
    public function filename_autofound()
    {
        $this->artisan('geonames:make:migration geonames')
             ->assertExitCode(0);

        $this->artisan('migrate:fresh')
             ->assertExitCode(0);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File [' . GeoDataImporter::storagePath('allCountries.txt') . '] not found.');

        $this->artisan('geonames:import:file-to-db', [
            'type' => 'geonames',
        ]);
    }

    /** @test */
    public function filename_autofound_with_suffix()
    {
        $this->artisan('geonames:make:migration geonames --suffix=foo')
             ->assertExitCode(0);

        $this->artisan('migrate:fresh')
             ->assertExitCode(0);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File [' . GeoDataImporter::storagePath('FOO.txt') . '] not found.');

        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'geonames',
            '--suffix' => 'foo',
        ]);
    }

    /** @test */
    public function import_real_data()
    {
        $this->artisan('geonames:download', [
            'files'     => [
                'AD.zip',
                'AI.zip',
            ],
            '--force'   => true,
            '--extract' => true,
        ])->assertExitCode(0);

        $this->artisan('geonames:make:migration geonames --suffix=ad')
             ->assertExitCode(0);

        $this->artisan('migrate:fresh')
             ->assertExitCode(0);

        $this->assertEquals(0, DB::table('geonames_ad')->count());

        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'geonames',
            '--suffix' => 'ad',
        ])->assertExitCode(0);

        $count = DB::table('geonames_ad')->count();
        $this->assertTrue($count > 500);

        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'geonames',
            'file'     => GeoDataImporter::storagePath('AI.txt'),
            '--suffix' => 'ad',
        ])->assertExitCode(0);

        $newCount = DB::table('geonames_ad')->count();
        $this->assertTrue($newCount > $count);

        $aiCount = $newCount - $count;
        $this->assertTrue($aiCount > 100);

        $this->artisan('geonames:import:file-to-db', [
            'type'       => 'geonames',
            'file'       => GeoDataImporter::storagePath('AI.txt'),
            '--suffix'   => 'ad',
            '--truncate' => true,
        ])->assertExitCode(0);

        $this->assertEquals($aiCount, DB::table('geonames_ad')->count());
    }
}
