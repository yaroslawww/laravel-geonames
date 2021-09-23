<?php

namespace LaraGeoData\Tests\Console;

use LaraGeoData\Tests\TestCase;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class MakeMigrationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Remove old migrations
        $finder = ( new Finder() )->files()->name([
            '*_geonames_*',
        ])->in(database_path('migrations/'));
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            unlink($file->getRealPath());
        }
    }

    /** @test */
    public function migrate_geonames_simple()
    {
        $finder = ( new Finder() )->files()->name([
            '*_geonames_*',
        ])->in(database_path('migrations/'));
        $this->assertFalse($finder->hasResults());

        $this->artisan('geonames:make:migration geonames')
             ->assertExitCode(0);

        $this->assertTrue($finder->hasResults());
        $this->assertEquals(1, iterator_count($finder->getIterator()));
    }

    /** @test */
    public function migrate_geonames_with_suffix()
    {
        $finder = ( new Finder() )->files()->name([
            '*_geonames_table_fr*',
        ])->in(database_path('migrations/'));
        $this->assertFalse($finder->hasResults());

        $this->artisan('geonames:make:migration geonames --suffix=fr')
             ->assertExitCode(0);

        $this->assertTrue($finder->hasResults());
        $this->assertEquals(1, iterator_count($finder->getIterator()));

        $iterator = $finder->getIterator();
        $iterator->rewind();
        /** @var SplFileInfo $file */
        $file    = $iterator->current();
        $content = file_get_contents($file->getRealPath());
        $this->assertStringContainsString('CreateGeonamesTableFr', $content);
        $this->assertStringContainsString(". '_fr'", $content);
    }
}
