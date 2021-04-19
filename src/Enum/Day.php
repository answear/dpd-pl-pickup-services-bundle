<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

use MabeEnum\Enum;

class Day extends Enum
{
    public const MONDAY = '1';
    public const TUESDAY = '2';
    public const WEDNESDAY = '3';
    public const THURSDAY = '4';
    public const FRIDAY = '5';
    public const SATURDAY = '6';
    public const SUNDAY = '7';

    public static function monday(): self
    {
        return static::get(static::MONDAY);
    }

    public static function tuesday(): self
    {
        return static::get(static::TUESDAY);
    }

    public static function wednesday(): self
    {
        return static::get(static::WEDNESDAY);
    }

    public static function thursday(): self
    {
        return static::get(static::THURSDAY);
    }

    public static function friday(): self
    {
        return static::get(static::FRIDAY);
    }

    public static function saturday(): self
    {
        return static::get(static::SATURDAY);
    }

    public static function sunday(): self
    {
        return static::get(static::SUNDAY);
    }
}
