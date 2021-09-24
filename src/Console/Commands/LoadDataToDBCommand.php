<?php

namespace LaraGeoData\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LaraGeoData\Facades\GeoDataImporter;

class LoadDataToDBCommand extends Command
{
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

    protected string $character = 'utf8mb4';

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
        $methodName = 'import' . Str::ucfirst(Str::camel($type)) . 'Data';
        if (!method_exists($this, $methodName)) {
            throw new \Exception("Import type [{$type}] not supported.");
        }

        return $this->{$methodName}();
    }

    /**
     * Process import for geonames.
     *
     * @return int
     * @throws \Exception
     */
    protected function importGeonamesData(): int
    {
        $tableName = $this->getTableName('geonames');
        $filePath  = $this->getFilePath('geonames');

        if ($this->option('truncate')) {
            DB::table($tableName)->truncate();
        }
        DB::connection(config('geonames.database.connection'))->statement(
            "LOAD DATA LOCAL INFILE '{$filePath}'
        INTO TABLE `{$tableName}`
        CHARACTER SET  '{$this->character()}'
        (geoname_id,
        name,
        ascii_name,
        alternate_names,
        lat,
        lng,
        fclass,
        fcode,
        country,
        cc2,
        admin1,
        admin2,
        admin3,
        admin4,
        population,
        elevation,
        gtopo30,
        timezone,
        moddate,
        @status,
        @created_at,
        @updated_at)
        SET status=2,created_at=NOW(),updated_at=NOW()"
        );

        return 0;
    }

    /**
     * Find table name.
     *
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
    protected function getTableName(string $type): string
    {
        $suffix = $this->getSuffix();

        $tableName = config("geonames.database.tables.{$type}");

        if (!$tableName) {
            throw new \Exception("Table name not found for type [{$type}]");
        }

        if ($suffix) {
            $tableName = "{$tableName}_{$suffix}";
        }

        if (!Schema::connection(config('geonames.database.connection'))->hasTable($tableName)) {
            throw new \Exception("Table [{$tableName}] not found. Maybe you need run migrations.");
        }

        return $tableName;
    }

    /**
     * Find file path.
     *
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
    protected function getFilePath(string $type): string
    {
        $filePath = $this->argument('file');

        if (!$filePath) {
            $filePath = match ($type) {
                'geonames' => $this->getGeonamesDefaultFilePath()
            };
        }

        if (!$filePath || !$this->files->exists($filePath)) {
            throw new \Exception("File [{$filePath}] not found.");
        }

        return $filePath;
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

    /**
     * Get default file path.
     *
     * @return string
     */
    protected function getGeonamesDefaultFilePath(): string
    {
        $suffix = $this->getSuffix();

        if (!$suffix) {
            return GeoDataImporter::storagePath('allCountries.txt');
        }

        return GeoDataImporter::storagePath(Str::upper($suffix) . '.txt');
    }

    /**
     * @return string
     */
    protected function character(): string
    {
        return $this->character;
    }
}
