<?php

namespace LaraGeoData\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeMigrationCommand extends Command
{
    use HasTablesClassesMap, HasSuffixOption;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geonames:make:migration
        {type : Migration type. }
        {--suffix= : Suffix used for specify country if need. }
        {--replaces=* : Replaces array}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish specific migrations.';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     *
     * @param  Filesystem  $files
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
        $replaces = [];
        foreach ($this->option('replaces') as $replace) {
            $key   = Str::before($replace, ':');
            $value = Str::after($replace, ':');
            if ($key && $value) {
                $replaces[$key] = $value;
            }
        }
        $this->makeTableObjectNameByType($this->argument('type'), $this->files)
             ->makeMigration($this->getSuffix(), $replaces);

        return 0;
    }
}
