<?php

namespace Database\Seeders;

use App\Models\Record;
use Illuminate\Database\Seeder;

/**
 * RecordSeeder
 *
 * Populates the records table with fake data for development and pagination testing.

 * Usage:
 *   php artisan db:seed --class=RecordSeeder           # default: 25 records
 *
 * To seed a custom amount on a fresh database, edit the COUNT constant below,
 * or call the factory directly from Tinker:
 *   php artisan tinker
 *   >>> \App\Models\Record::factory()->count(50)->create()
 */
class RecordSeeder extends Seeder
{
    // Number of fake records to generate on a fresh database.
    // 25 gives 3 pages (10 + 10 + 5) — enough to exercise the pagination controls.
    private const COUNT = 25;

    public function run(): void
    {
        // Guard: only seed when the table is completely empty.
        // This prevents duplicate records from being added on every app startup.
        if (Record::count() > 0) {
            $this->command->info('[SKIP] Records table already has data. Seeder skipped.');
            return;
        }

        $this->command->info('Seeding ' . self::COUNT . ' fake records...');

        Record::factory()->count(self::COUNT)->create();

        $this->command->info('[OK] ' . self::COUNT . ' records inserted.');
    }
}

