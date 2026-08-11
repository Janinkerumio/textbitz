<?php

namespace App\Services\Data;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Enums\DefaultSeeder;

class DefaultDataSeeder
{

    public static function ensureDatabaseMigrated(): void
    {
        if(self::hasPendingMigrations())
        {
            Artisan::call('migrate', [
                '--force' => true
            ]);
        }
    }

    public static function hasPendingMigrations(): bool
    {
        if (! Schema::hasTable('migrations'))
        {
            return true;
        }

        $ran = DB::table('migrations')->pluck('migration')->all();

        $files = collect(app('migrator')->getMigrationFiles(
            database_path('migrations')
        ))->keys();

        return $files->diff($ran)->isNotEmpty();
    }

    /**
     * @param string $table - Table name
     * @param class-string<\Illuminate\Database\Seeder> $class
     */
    public static function initiateSeeder(string $table, string $class): bool
    {
        try {
            if(Schema::hasTable($table) && DB::table($table)->exists())
            {
                return true;
            }

            $exitCode = Artisan::call('db:seed', [
                        '--class' => $class,
                        '--force' => true
                    ]);

            return $exitCode === 0;
        } catch (\Exception $e) {
            report($e);

            return false;
        }
    }

    public static function dataSeed(bool $seedDemoData = false): void
    {
        self::ensureDatabaseMigrated();

        if(!cache()->has('data_seeded'))
        {
            $configured = $seedDemoData 
                            ? config('app.initiate_db_seed', []) 
                            : array_intersect(config('app.initiate_db_seed', []), ['templates']);

            $anyAttempted = false;
            $allSucceeded = true;

            foreach (DefaultSeeder::cases() as $defaultSeeder)
            {
                if(in_array($defaultSeeder->value, $configured, true))
                {
                    $anyAttempted = true;

                    if (!self::initiateSeeder($defaultSeeder->value, $defaultSeeder->seeder()))
                    {
                        $allSucceeded = false;
                    }
                }
            }

            if($anyAttempted && $allSucceeded)
            {
                cache()->forever('data_seeded', true);
            }
        }
    }
}
