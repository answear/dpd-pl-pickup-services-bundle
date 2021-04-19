<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

use MabeEnum\Enum;

class Service extends Enum
{
    public const DELIVERY = '100';
    public const COD = '101';
    public const SWAP = '300';
    public const DROPOFF_ONLINE = '200';
    public const DROPOFF_OFFLINE = '201';
    public const MONITORED_DOCUMENTS = 'P10';
    public const ROD = 'P20';
    public const TYRES = 'P30';
    public const PALLET = 'P90';

    public static function delivery(): self
    {
        return static::get(static::DELIVERY);
    }

    public static function cod(): self
    {
        return static::get(static::COD);
    }

    public static function swap(): self
    {
        return static::get(static::SWAP);
    }

    public static function dropoffOnline(): self
    {
        return static::get(static::DROPOFF_ONLINE);
    }

    public static function dropoffOffline(): self
    {
        return static::get(static::DROPOFF_OFFLINE);
    }

    public static function monitoredDocuments(): self
    {
        return static::get(static::MONITORED_DOCUMENTS);
    }

    public static function rod(): self
    {
        return static::get(static::ROD);
    }

    public static function tyres(): self
    {
        return static::get(static::TYRES);
    }

    public static function pallet(): self
    {
        return static::get(static::PALLET);
    }
}
