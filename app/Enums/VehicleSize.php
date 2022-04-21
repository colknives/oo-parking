<?php

namespace App\Enums;

final class VehicleSize
{
    const SMALL        = 0;
    const MEDIUM       = 1;
    const LARGE        = 2;

    const SMALL_NAME        = 'Small';
    const MEDIUM_NAME       = 'Medium';
    const LARGE_NAME        = 'Large';

    public static $sizes = [self::SMALL, self::MEDIUM, self::LARGE];

    public static $typeNames = [self::SMALL_NAME, self::MEDIUM_NAME, self::LARGE_NAME];
}
