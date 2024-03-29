<?php

namespace LaraGeoData\Tests\Console;

use Illuminate\Support\Facades\DB;
use LaraGeoData\Tests\TestCase;

class TablesChecksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh')->assertExitCode(0);
    }

    /** @test  */
    public function load_geonames()
    {
        $tables = collect(DB::select('SHOW TABLES'))->map(function ($val) {
            foreach ($val as $tbl) {
                return $tbl;
            }

            return null;
        })->filter()->unique()->toArray();
        $this->assertFalse(in_array('geonames_wf', $tables));

        $this->artisan('geonames:make:migration geonames --suffix=wf')
             ->assertExitCode(0);
        $this->artisan('migrate:fresh')->assertExitCode(0);

        $tables = collect(DB::select('SHOW TABLES'))->map(function ($val) {
            foreach ($val as $tbl) {
                return $tbl;
            }

            return null;
        })->filter()->unique()->toArray();

        $this->assertTrue(in_array('gn_geonames_wf', $tables));

        DB::table('gn_geonames_wf')->insert([
            'geoname_id' => 23,
        ]);
        DB::table('gn_geonames_wf')->insert([
            'geoname_id' => 63,
        ]);

        $this->assertEquals(2, DB::table('gn_geonames_wf')->count());
    }
}
