<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Enums\DefaultSeeder;

class DefaultDataSeeder
{

    public static function ensureDatabaseMigrated(): bool
    {
        if(!cache()->has('database_seeded'))
        {
            Artisan::call('migrate', [
                '--force' => true
            ]);

            cache()->forever('database_seeded', true);

            return true;
        }

        return false;
    }

    /**
     * @param string $table - Table name
     * @param class-string<\Illuminate\Database\Seeder> $class
     */
    public static function initiateSeeder($table, $class): void
    {
        if(! DB::table($table)->exists())
        {
            Artisan::call('db:seed', [
                '--class' => $class,
                '--force' => true
            ]);
        }
    }

    public static function dataSeed(): void
    {
        if(self::ensureDatabaseMigrated())
        {
            $configured = config('app.initiate_db_seed', []);

            foreach (DefaultSeeder::cases() as $defaultSeeder)
            {
                if(in_array($defaultSeeder->value, $configured, true))
                {
                    self::initiateSeeder($defaultSeeder->value, $defaultSeeder->seeder());
                }
            }
        }
    }
}
