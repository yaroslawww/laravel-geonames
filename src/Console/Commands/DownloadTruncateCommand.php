<?php

namespace LaraGeoData\Console\Commands;

use Illuminate\Console\Command;
use LaraGeoData\Facades\GeoDataImporter;

class DownloadTruncateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geonames:download:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all downloaded files.';


    public function handle()
    {
        GeoDataImporter::storageTruncate();

        $this->info('Directory truncated: ' . GeoDataImporter::storagePath());

        return 0;
    }
}
