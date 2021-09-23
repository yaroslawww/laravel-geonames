<?php

namespace LaraGeoData\Tests\Console;

use LaraGeoData\Tests\TestCase;

class DownloadTruncateCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function successful_truncate_file()
    {
        $path            = rtrim(config('geonames.storage.path'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $pathPostalCodes = $path . trim(config('geonames.storage.postal_codes_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

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

        $this->artisan('geonames:download:truncate')
             ->assertExitCode(0);

        $this->assertFalse(file_exists($path . 'WF.zip'));
        $this->assertFalse(file_exists($pathPostalCodes . 'WF.zip'));
    }
}
