<?php

namespace App\Enums;

final class HourlyRate
{
    const SMALL        = 20.00;
    const MEDIUM       = 60.00;
    const LARGE        = 100.00;
    const FLAT         = 40.00;
    const WHOLEDAY     = 5000.00;

    public static $prices = [self::SMALL, self::MEDIUM, self::LARGE];
}
