<?php

namespace LaraGeoData\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use LaraGeoData\Facades\GeoDataImporter;

class DownloadFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geonames:download
        {files?* : Files to download. }
        {--postal=* : Postal files to download. }
        {--force : Override files any if exists. }
        {--extract : Extract archive. }
        {--defaults : ODownload default files set. }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and extract files from server.';


    public function handle()
    {
        foreach ($this->getFiles() as $file) {
            $this->downloadGeneralFile($file);
            $this->extractFile($file);
        }

        foreach ($this->getPostalFiles() as $file) {
            $this->downloadPostalCodeFile($file);
            $this->extractFile($this->postalCodeFile($file));
        }

        return 0;
    }

    /**
     * Get default files.
     *
     * @return array
     */
    protected function getFiles(): array
    {
        $files = $this->argument('files');
        if (empty($files) && $this->option('defaults')) {
            $files = [
                'allCountries.zip',
                'alternateNames.zip',
                'hierarchy.zip',
                'admin1CodesASCII.txt',
                'admin2Codes.txt',
                'featureCodes_en.txt',
                'timeZones.txt',
                'countryInfo.txt',
            ];
        }
        if (!is_array($files)) {
            throw new \InvalidArgumentException('"files" argument should be valid array.');
        }

        return $files;
    }

    /**
     * Get default files.
     *
     * @return array
     */
    protected function getPostalFiles(): array
    {
        $files = $this->option('postal');
        if (is_array($files)) {
            $files = array_filter($files);
        }
        if (empty($files) && $this->option('defaults')) {
            $files = [
                'allCountries.zip',
            ];
        }
        if (!is_array($files)) {
            throw new \InvalidArgumentException('"postal" option should be valid array.');
        }

        return $files;
    }

    protected function downloadGeneralFile(string $file)
    {
        $baseUrl = rtrim(config('geonames.import_repos.general'), '/') . '/';
        $url     = $baseUrl . $file;
        $this->downloadFile($file, $url);
    }

    protected function downloadPostalCodeFile(string $file)
    {
        $baseUrl = rtrim(config('geonames.import_repos.zip'), '/') . '/';
        $url     = $baseUrl . $file;
        $this->downloadFile($this->postalCodeFile($file), $url);
    }

    protected function downloadFile(string $file, string $url)
    {
        $this->info("Download: {$url} to {$file}");
        $bar = $this->output->createProgressBar(10000);
        $bar->start();
        $result = GeoDataImporter::storageCreateFromUrl(
            $url,
            $file,
            function ($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($bar) {
                if ($download_size > 0) {
                    $percent = round($downloaded / $download_size * 10000);
                    $bar->setProgress($percent);
                }
                sleep(1);
            },
            (bool) $this->option('force')
        );
        $bar->finish();
        if ($result) {
            $this->info(' Downloaded.');
        } else {
            $this->error(' Skipped.');
        }
    }

    protected function postalCodeFile(string $file): string
    {
        return rtrim(config('geonames.storage.postal_codes_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }


    protected function extractFile(string $file)
    {
        if (!$this->option('extract')) {
            return;
        }
        if (!Str::endsWith($file, '.zip')) {
            return;
        }
        $result = GeoDataImporter::storageExtractZipFile($file);
        if ($result) {
            $this->info("Extracted file: {$file}");
        } else {
            $this->error("Error extraction file: {$file}");
        }
    }
}
