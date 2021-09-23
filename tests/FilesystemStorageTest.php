<?php

namespace LaraGeoData\Tests;

use Illuminate\Support\Facades\Config;
use LaraGeoData\Storage\FilesystemStorage;

class FilesystemStorageTest extends TestCase
{
    protected string $folderPath = 'build/test_folder';

    protected function setUp(): void
    {
        parent::setUp();
        app('files')->delete($this->folderPath);
        app('files')->deleteDirectory($this->folderPath);
    }

    /** @test */
    public function prepare_directory_can_return_exception()
    {
        app('files')->put($this->folderPath, 'lorem content');
        $storage = new FilesystemStorage(app('files'), $this->folderPath);

        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('mkdir(): File exists');
        $storage->prepareDirectory();
    }

    /** @test */
    public function store_from_url()
    {
        Config::set('geonames.storage.download_provider', 'curl_php');
        $storage = new FilesystemStorage(app('files'), $this->folderPath);
        $this->assertFalse($storage->exists('my_file.file'));

        $storage->storeFromUrl(url('/my_file.file'), null, function () {
            $this->assertTrue(true);
        });

        $this->assertTrue($storage->exists('my_file.file'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error [6]. See https://php.net/manual/en/function.curl-errno.php');
        $storage->storeFromUrl('https://not-exists.home/my_file.not');
    }

    /** @test */
    public function store_url_not_valid_provider()
    {
        Config::set('geonames.storage.download_provider', 'curl');
        $storage = new FilesystemStorage(app('files'), $this->folderPath);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Current download provider not supported');
        $storage->storeFromUrl(url('/my_file.php'));
    }

    /** @test */
    public function extract_zip_file_exception()
    {
        Config::set('geonames.storage.download_provider', 'curl');
        $storage = new FilesystemStorage(app('files'), $this->folderPath);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Zip open ['.$this->folderPath.'/not_exists.file] error: 9. See: https://php.net/manual/en/zip.constants.php');
        $storage->extractZipFile('not_exists.file');
    }
}
