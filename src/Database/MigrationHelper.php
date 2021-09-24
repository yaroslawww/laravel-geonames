<?php

namespace LaraGeoData\Database;

use Illuminate\Database\Schema\Blueprint;

class MigrationHelper
{

    /**
     * @param Blueprint $table
     */
    public static function geonamesDefaultColumns(Blueprint $table): void
    {
        $table->unsignedInteger('geoname_id');
        $table->string('name', 200)->nullable()->index();
        $table->string('ascii_name', 200)->nullable()->index();
        $table->text('alternate_names')->nullable();
        $table->decimal('lat', 10, 7)->nullable()->index();
        $table->decimal('lng', 10, 7)->nullable()->index();
        $table->char('fclass', 1)->nullable()->index();
        $table->string('fcode', 10)->nullable()->index();
        $table->string('country', 2)->nullable()->index();
        $table->string('cc2', 200)->nullable()->index();
        $table->string('admin1', 20)->nullable()->index();
        $table->string('admin2', 80)->nullable()->index();
        $table->string('admin3', 20)->nullable()->index();
        $table->string('admin4', 20)->nullable()->index();
        $table->unsignedInteger('population')->nullable()->index();
        $table->integer('elevation')->nullable()->index();
        $table->integer('gtopo30')->nullable()->index();
        $table->string('timezone', 40)->nullable()->index();
        $table->dateTime('moddate')->nullable()->index();
        $table->unsignedSmallInteger('status')->default(1);
        $table->nullableTimestamps();

        $table->primary('geoname_id');
    }
}
