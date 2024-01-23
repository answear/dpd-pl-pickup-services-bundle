<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

use MabeEnum\Enum;

class Type extends Enum
{
    public const STANDARD = '100';
    public const CHAIN = '200';
    public const DPD = '300';
    public const SWIP_BOX_1 = '400401';
    public const SWIP_BOX_2 = '400402';
    public const POINT_PACK = '500501';
    public const FOREIGN_LOCKER = '400';

    public static function ifEmpty(): self
    {
        return self::standard();
    }

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

    public static function swipBox1(): self
    {
        return self::get(self::SWIP_BOX_1);
    }

    public static function swipBox2(): self
    {
        return self::get(self::SWIP_BOX_2);
    }

    public static function pointPack(): self
    {
        return self::get(self::POINT_PACK);
    }

    public static function foreignLocker(): self
    {
        return self::get(self::FOREIGN_LOCKER);
    }
}
