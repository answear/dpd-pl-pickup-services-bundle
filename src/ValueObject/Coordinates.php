<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

use Webmozart\Assert\Assert;

class Coordinates
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        Assert::range($latitude, -90, 90);
        Assert::range($longitude, -180, 180);
    }
}
