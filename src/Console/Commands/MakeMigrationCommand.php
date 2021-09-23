<?php

namespace LaraGeoData\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geonames:make:migration
        {type : Migration type. }
        {--suffix= : Suffix used for specify country if need. }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish specific migrations.';

    /**
     * Path where stored all templates.
     *
     * @var string
     */
    protected string $templatesPath = __DIR__ . '/../../../database/migrations/';

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
        $type       = $this->argument('type');
        $methodName = 'make' . Str::ucfirst(Str::camel($type)) . 'Migration';
        if (!method_exists($this, $methodName)) {
            throw new \Exception("Migration type [{$type}] not supported");
        }

        return $this->{$methodName}();
    }

    protected function makeGeonamesMigration(): int
    {
        $suffix = $this->option('suffix');
        if ($suffix) {
            $suffix = Str::snake($suffix);
        }

        $template = $this->templatesPath . 'create_geonames_table.php.stub';
        $saveTo   = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_geonames_table' . ($suffix ? "_{$suffix}" : '') . '.php');

        $this->files->copy($template, $saveTo);

        $text = $this->files->get($saveTo);
        $text = str_replace('/** class_suffix **/', $suffix ? Str::ucfirst(Str::camel($suffix)) : '', $text);
        $text = str_replace('/** table_suffix **/', $suffix ? " . '_{$suffix}'" : '', $text);
        $this->files->put($saveTo, $text);

        return 0;
    }
}
