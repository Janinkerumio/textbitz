<?php

namespace App\Enums;

enum InertiaControllerNames: string
{
    case Dashboard = 'Dashboard';
    case Contact = 'Contact';
    case Blast = 'Blast';
    case History = 'History';
    case Templates = 'Templates';
    case Settings = 'Settings';
    case Recipients = 'Recipients';

    /**
     * @return class-string<\App\Http\Controllers\Controller>
     */
    public function controller(): string
    {
        return match ($this) {
            self::Dashboard => \App\Http\Controllers\DashboardController::class,
            self::Contact => \App\Http\Controllers\ContactController::class,
            self::Blast => \App\Http\Controllers\BlastController::class,
            self::History => \App\Http\Controllers\HistoryController::class,
            self::Templates => \App\Http\Controllers\TemplatesController::class,
            self::Settings => \App\Http\Controllers\SettingsController::class,
            self::Recipients => \App\Http\Controllers\RecipientsController::class,
        };
    }
}
