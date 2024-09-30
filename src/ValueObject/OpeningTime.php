<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Day;

class OpeningTime
{
    public function __construct(
        public Day $day,
        public string $from,
        public string $to,
    ) {
    }
}
