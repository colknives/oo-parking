<?php

namespace App\Enums;

final class VehicleSize
{
    const SMALL        = 1;
    const MEDIUM       = 2;
    const LARGE        = 3;

    public static $sizes = [self::SMALL, self::MEDIUM, self::LARGE];
}
