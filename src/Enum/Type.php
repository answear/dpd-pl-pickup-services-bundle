<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

use MabeEnum\Enum;

class Type extends Enum
{
    public const STANDARD = 100;
    public const CHAIN = 200;
    public const DPD = 300;

    public static function standard(): self
    {
        return self::get(self::STANDARD);
    }

    public static function chain(): self
    {
        return self::get(self::CHAIN);
    }

    public static function dpd(): self
    {
        return self::get(self::DPD);
    }
}
