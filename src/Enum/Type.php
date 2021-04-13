<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

use MabeEnum\Enum;

class Type extends Enum
{
    public const STANDARD = 100;
    public const NETWORK = 200;
    public const FULL_TIME = 300;

    public static function standard(): self
    {
        return self::get(self::STANDARD);
    }

    public static function network(): self
    {
        return self::get(self::NETWORK);
    }

    public static function fullTime(): self
    {
        return self::get(self::FULL_TIME);
    }
}
