<?php

namespace LaraGeoData\Tests\Console;

use Illuminate\Support\Facades\DB;
use LaraGeoData\Facades\GeoDataImporter;
use LaraGeoData\Tests\TestCase;

class LoadDataToDBCommandPostalcodesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh')->assertExitCode(0);
    }

    /** @test */
    public function filename_autofound()
    {
        $this->artisan('geonames:make:migration postalcodes')
             ->assertExitCode(0);

        $this->artisan('migrate:fresh')
             ->assertExitCode(0);

        GeoDataImporter::storageTruncate();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File [' . GeoDataImporter::storagePath('postal_codes/allCountries.txt') . '] not found.');

        $this->artisan('geonames:import:file-to-db', [
            'type' => 'postalcodes',
        ])->assertExitCode(0);
    }

    /** @test */
    public function filename_autofound_with_suffix()
    {
        $this->artisan('geonames:make:migration postalcodes --suffix=foo')
             ->assertExitCode(0);

        $this->artisan('migrate:fresh')
             ->assertExitCode(0);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File [' . GeoDataImporter::storagePath('postal_codes/FOO.txt') . '] not found.');

        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'postalcodes',
            '--suffix' => 'foo',
        ]);
    }

    /** @test */
    public function import_real_data()
    {
        $this->artisan('geonames:download', [
            '--postal'     => [
                'AD.zip',
                'AS.zip',
            ],
            '--force'   => true,
            '--extract' => true,
        ])->assertExitCode(0);

        $this->artisan('geonames:make:migration postalcodes --suffix=ad')
             ->assertExitCode(0);

        $this->artisan('migrate:fresh')
             ->assertExitCode(0);

        $this->assertEquals(0, DB::table('gn_postal_codes_ad')->count());

        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'postalcodes',
            '--suffix' => 'ad',
        ])->assertExitCode(0);

        $count = DB::table('gn_postal_codes_ad')->count();
        $this->assertTrue($count == 7);

        $this->artisan('geonames:import:file-to-db', [
            'type'     => 'postalcodes',
            'file'     => GeoDataImporter::storagePath('postal_codes/AS.txt'),
            '--suffix' => 'ad',
        ])->assertExitCode(0);

        $newCount = DB::table('gn_postal_codes_ad')->count();
        $this->assertTrue($newCount == ($count + 1));

        $aiCount = $newCount - $count;
        $this->assertTrue($aiCount == 1);

        $this->artisan('geonames:import:file-to-db', [
            'type'       => 'postalcodes',
            'file'       => GeoDataImporter::storagePath('postal_codes/AS.txt'),
            '--suffix'   => 'ad',
            '--truncate' => true,
        ])->assertExitCode(0);

        $this->assertEquals($aiCount, DB::table('gn_postal_codes_ad')->count());
    }
}
