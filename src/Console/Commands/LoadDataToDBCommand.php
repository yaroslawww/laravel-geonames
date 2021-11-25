<?php

namespace LaraGeoData\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class LoadDataToDBCommand extends Command
{
    use HasTablesClassesMap;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geonames:import:file-to-db
        {type : Migration type. }
        {file? : Csv file path. }
        {--suffix= : Suffix used for specify country if need. }
        {--truncate : Truncate table before import. }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload data from csv file to DB.';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $tableClassName = $this->getTableClassNameByType($this->argument('type'));

        (new $tableClassName($this->files))->loadData($this->argument('file'), $this->getSuffix(), (bool) $this->option('truncate'));

        return 0;
    }


    /**
     * Get formatted suffix.
     *
     * @return string|null
     */
    protected function getSuffix(): ?string
    {
        $suffix = $this->option('suffix');
        if ($suffix) {
            $suffix = Str::snake($suffix);
        }

        return $suffix;
    }
}
