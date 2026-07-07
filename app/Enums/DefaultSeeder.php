<?php

namespace App\Enums;

enum DefaultSeeder: string
{
    case Contacts = 'contacts';

    /**
     * @return class-string<\Illuminate\Database\Seeder>
     */

    public function seeder(): string
    {
        return match ($this) {
            self::Contacts => \Database\Seeders\ContactSeeder::class,
        };
    }
}