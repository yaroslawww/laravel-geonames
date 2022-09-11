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

    /**
     * @param Blueprint $table
     */
    public static function postalcodesDefaultColumns(Blueprint $table): void
    {
        $table->string('postal_code', 50)->index();
        $table->string('country_code', 10)->index();
        $table->string('place_name', 100)->nullable();
        $table->string('admin_name1', 100)->nullable();
        $table->string('admin_code1', 80)->nullable()->index();
        $table->string('admin_name2', 100)->nullable();
        $table->string('admin_code2', 80)->nullable()->index();
        $table->string('admin_name3', 100)->nullable();
        $table->string('admin_code3', 80)->nullable()->index();
        $table->decimal('lat', 10, 7)->nullable()->index();
        $table->decimal('lng', 10, 7)->nullable()->index();
        $table->unsignedSmallInteger('accuracy')->default(0);
        $table->unsignedSmallInteger('status')->default(1);
        $table->nullableTimestamps();

        $table->primary('postal_code');
    }
}
