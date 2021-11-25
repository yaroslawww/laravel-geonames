<?php

namespace LaraGeoData\Database\Tables;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LaraGeoData\Facades\GeoDataImporter;

abstract class Table implements GeoTable
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Path where stored all templates.
     *
     * @var string
     */
    protected string $migrationTemplatesPath = __DIR__ . '/../../../database/migrations/';

    /**
     * Data file path prefix.
     *
     * @var string
     */
    protected $defaultDataFilePathPrefix = '';

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @inheritDoc
     */
    public function makeMigration(?string $suffix = null): string
    {
        $templateNameRoot = $this->getTemplateNameRoot();
        $template         = $this->migrationTemplatesPath . $templateNameRoot . '.php.stub';
        $saveTo           = database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $templateNameRoot . ($suffix ? "_{$suffix}" : '') . '.php');

        $this->files->copy($template, $saveTo);

        $text = $this->files->get($saveTo);
        $text = str_replace('/** class_suffix **/', $suffix ? Str::ucfirst(Str::camel($suffix)) : '', $text);
        $text = str_replace('/** table_suffix **/', $suffix ? " . '_{$suffix}'" : '', $text);
        $this->files->put($saveTo, $text);

        return $saveTo;
    }

    /**
     * Get migration template file name (only root part).
     *
     * @return string
     */
    abstract public function getTemplateNameRoot(): string;

    /**
     * Get table name (only root part).
     *
     * @return string
     */
    abstract public function getTableNameRoot(): string;

    /**
     * @inheritDoc
     */
    public function loadData(?string $filePath = null, ?string $suffix = null, $truncate = true)
    {
        $tableName = $this->getTableName($suffix);
        $filePath  = $this->getDataFilePath($suffix, $filePath);

        if ($truncate) {
            DB::table($tableName)->truncate();
        }

        DB::connection(config('geonames.database.connection'))
          ->statement($this->prepareLoadStatement($tableName, $filePath));
    }

    protected function getDataFilePath(?string $suffix, ?string $filePath): string
    {
        $filePath = $filePath ?: $this->getDefaultDataFilePath($suffix);

        throw_if(!$filePath, new \Exception('File not found.'));

        if (!$this->files->exists($filePath)) {
            $initialFilePath = $filePath;
            $filePath        = GeoDataImporter::storagePath($filePath);

            if (!$this->files->exists($filePath)) {
                throw new \Exception("File [{$initialFilePath}] not found.");
            }
        }

        return $filePath;
    }

    protected function getDefaultDataFilePath(?string $suffix): string
    {
        if (!$suffix) {
            return GeoDataImporter::storagePath($this->defaultDataFilePathPrefix . 'allCountries.txt');
        }

        return GeoDataImporter::storagePath($this->defaultDataFilePathPrefix . Str::upper($suffix) . '.txt');
    }

    protected function getTableName(?string $suffix): string
    {
        $tableName = $this->getTableNameRoot();

        if ($suffix) {
            $tableName = "{$tableName}_{$suffix}";
        }

        if (!Schema::connection(config('geonames.database.connection'))->hasTable($tableName)) {
            throw new \Exception("Table [{$tableName}] not found. Maybe you need run migrations.");
        }

        return $tableName;
    }

    protected function prepareLoadStatement(string $tableName, string $filePath): string
    {
        $columnsNames = implode(',', $this->orderedColumnsListToLoad());

        return "LOAD DATA LOCAL INFILE '{$filePath}'
        INTO TABLE `{$tableName}`
        CHARACTER SET  '{$this->character()}'
        ({$columnsNames},
        @status,
        @created_at,
        @updated_at)
        SET status=2,created_at=NOW(),updated_at=NOW()";
    }

    abstract protected function orderedColumnsListToLoad(): array;

    /**
     * @return string
     */
    protected function character(): string
    {
        return 'utf8mb4';
    }
}
