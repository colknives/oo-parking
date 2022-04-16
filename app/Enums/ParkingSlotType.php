<?php

namespace App\Enums;

final class ParkingSlotType
{
    const SMALL        = 1;
    const MEDIUM       = 2;
    const LARGE        = 3;

    public static $types = [self::SMALL, self::MEDIUM, self::LARGE];
}
