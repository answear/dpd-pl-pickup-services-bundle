<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

class Coordinates
{
    public float $latitude;
    public float $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        // @todo validation
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
