<?php

namespace App\Enums;

enum DefaultSeeder: string
{
    case Contacts = 'contacts';
    case Users = 'users';
    case Templates = 'templates';
    case History = 'history';

    /**
     * @return class-string<\Illuminate\Database\Seeder>
     */
    public function seeder(): string
    {
        return match ($this) {
            self::Users => \Database\Seeders\UserSeeder::class,
            self::Templates => \Database\Seeders\TemplateSeeder::class,
            self::Contacts => \Database\Seeders\ContactSeeder::class,
            self::History => \Database\Seeders\HistorySeeder::class,
        };
    }
}