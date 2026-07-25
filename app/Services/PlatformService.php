<?php

namespace App\Services;

use Native\Mobile\Facades\System;

class PlatformService
{
    protected static ?array $platform = null;

    public static function isRunningNatively(): bool
    {
        return function_exists('native_dispatch')
            || extension_loaded('nativephp')
            || extension_loaded('native_php');
    }

    public static function detect(): array
    {
        if (self::$platform !== null) {
            return self::$platform;
        }

        if(!self::isRunningNatively())
        {
            return self::$platform = ['isAndroid' => false, 'isIos' => false];
        }

        try {
            return self::$platform = [
                'isAndroid' => System::isAndroid(),
                'isIos' => System::isIos(),
            ];
        } catch (\Throwable $e) {
            return self::$platform = ['isAndroid' => false, 'isIos' => false];
        }
    }
}
