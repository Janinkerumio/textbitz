<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DefaultDataSeeder
{

    private static function isDatabaseMigrated()
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

    public static function initiateSeeder(string $table, string $class)
    {
        if(self::isDatabaseMigrated() && !DB::table($table)->exists())
        {
            Artisan::call('db:seed', [
                '--class' => $class,
                '--force' => true
            ]);
        }
    }
}
