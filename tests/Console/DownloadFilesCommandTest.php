<?php

namespace LaraGeoData\Tests\Console;

use Illuminate\Support\Str;
use LaraGeoData\Facades\GeoDataImporter;
use LaraGeoData\Tests\TestCase;

class DownloadFilesCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        GeoDataImporter::storageTruncate();
    }

    /** @test */
    public function successful_download_file()
    {
        $path            = rtrim(config('geonames.storage.path'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $pathPostalCodes = $path . trim(config('geonames.storage.postal_codes_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->assertFalse(file_exists($path . 'WF.zip'));
        $this->assertFalse(file_exists($path . 'UM.zip'));
        $this->assertFalse(file_exists($pathPostalCodes . 'WF.zip'));
        $this->assertFalse(file_exists($pathPostalCodes . 'VI.zip'));

        $this->artisan('geonames:download', [
            'files'    => [
                'WF.zip',
                'UM.zip',
            ],
            '--postal' => [
                'WF.zip',
                'VI.zip',
            ],
        ])->assertExitCode(0);

        $this->assertTrue(file_exists($path . 'WF.zip'));
        $this->assertTrue(file_exists($path . 'UM.zip'));
        $this->assertTrue(file_exists($pathPostalCodes . 'WF.zip'));
        $this->assertTrue(file_exists($pathPostalCodes . 'VI.zip'));
    }

    /** @test */
    public function do_not_restore_existings_files()
    {
        $path            = rtrim(config('geonames.storage.path'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $pathPostalCodes = $path . trim(config('geonames.storage.postal_codes_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->assertFalse(file_exists($path . 'WF.zip'));
        $this->assertFalse(file_exists($pathPostalCodes . 'WF.zip'));

        $this->artisan('geonames:download', [
            'files'    => [
                'WF.zip',
            ],
            '--postal' => [
                'WF.zip',
            ],
        ])->assertExitCode(0);

        $this->assertTrue(file_exists($path . 'WF.zip'));
        $this->assertTrue(file_exists($pathPostalCodes . 'WF.zip'));

        $randContent = 165423;

        $this->assertFalse(Str::endsWith(file_get_contents($path . 'WF.zip'), $randContent));
        $this->assertFalse(Str::endsWith(file_get_contents($pathPostalCodes . 'WF.zip'), $randContent));
        file_put_contents($path . 'WF.zip', $randContent, FILE_APPEND | LOCK_EX);
        file_put_contents($pathPostalCodes . 'WF.zip', $randContent, FILE_APPEND | LOCK_EX);


        $this->artisan('geonames:download', [
            'files'    => [
                'WF.zip',
            ],
            '--postal' => [
                'WF.zip',
            ],
        ])->assertExitCode(0);

        $this->assertTrue(Str::endsWith(file_get_contents($path . 'WF.zip'), $randContent));
        $this->assertTrue(Str::endsWith(file_get_contents($pathPostalCodes . 'WF.zip'), $randContent));

        $this->artisan('geonames:download', [
            'files'    => [
                'WF.zip',
            ],
            '--postal' => [
                'WF.zip',
            ],
            '--force'  => true,
        ])->assertExitCode(0);

        $this->assertFalse(Str::endsWith(file_get_contents($path . 'WF.zip'), $randContent));
        $this->assertFalse(Str::endsWith(file_get_contents($pathPostalCodes . 'WF.zip'), $randContent));
    }

    /** @test */
    public function successful_download_and_extract_file()
    {
        $path            = rtrim(config('geonames.storage.path'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $pathPostalCodes = $path . trim(config('geonames.storage.postal_codes_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->assertFalse(file_exists($path . 'WF.zip'));
        $this->assertFalse(file_exists($path . 'UM.zip'));
        $this->assertFalse(file_exists($pathPostalCodes . 'WF.zip'));
        $this->assertFalse(file_exists($pathPostalCodes . 'VI.zip'));

        $this->artisan('geonames:download', [
            'files'    => [
                'WF.zip',
                'UM.zip',
            ],
            '--postal' => [
                'WF.zip',
                'VI.zip',
            ],
            '--extract' => true,
        ])->assertExitCode(0);

        $this->assertTrue(file_exists($path . 'WF.zip'));
        $this->assertTrue(file_exists($path . 'UM.zip'));
        $this->assertTrue(file_exists($pathPostalCodes . 'WF.zip'));
        $this->assertTrue(file_exists($pathPostalCodes . 'VI.zip'));

        $this->assertTrue(file_exists($path . 'WF.txt'));
        $this->assertTrue(file_exists($path . 'UM.txt'));
        $this->assertTrue(file_exists($path . 'readme.txt'));
        $this->assertTrue(file_exists($pathPostalCodes . 'WF.txt'));
        $this->assertTrue(file_exists($pathPostalCodes . 'VI.txt'));
        $this->assertTrue(file_exists($pathPostalCodes . 'readme.txt'));
    }
}
