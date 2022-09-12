<?php

namespace LaraGeoData\Database\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminAreaViewCreator
{
    protected int $level = 1;

    protected ?string $suffix = null;

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function setSuffix(?string $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Create view
     *
     * @return void
     */
    public function up()
    {
        DB::connection(config('geonames.database.connection'))
          ->statement($this->dropView());

        DB::connection(config('geonames.database.connection'))
          ->statement($this->createView());
    }

    /**
     * Delete view
     *
     * @return void
     */
    public function down()
    {
        DB::connection(config('geonames.database.connection'))
          ->statement($this->dropView());
    }

    protected function viewName(): string
    {
        $tableName = config('geonames.database.tables.admin_areas');
        $tableName = Str::replace('{level}', $this->level, $tableName);
        if ($suffix = $this->suffix) {
            $tableName = "{$tableName}_{$suffix}";
        }

        return $tableName;
    }

    protected function tableName(): string
    {
        $tableName = config('geonames.database.tables.postalcodes');
        if ($suffix = $this->suffix) {
            $tableName = "{$tableName}_{$suffix}";
        }

        return $tableName;
    }

    /**
     * Internal script to delete view.
     *
     * @return string
     */
    protected function dropView(): string
    {
        return <<<SQL
DROP VIEW IF EXISTS `{$this->viewName()}`;
SQL;
    }

    /**
     * Internal script to create view
     *
     * @return string
     */
    protected function createView(): string
    {
        return <<<SQL
CREATE VIEW `{$this->viewName()}` AS

SELECT `admin_code{$this->level}` as admin_code, `admin_name{$this->level}` as admin_name
FROM `{$this->tableName()}`
WHERE `admin_code{$this->level}` <> ''
   OR `admin_name{$this->level}` <> ''
GROUP BY `admin_code{$this->level}`, `admin_name{$this->level}`;
SQL;
    }
}
