<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Day;

class OpeningTime
{
    public Day $day;
    public string $from;
    public string $to;

    public function __construct(Day $day, string $from, string $to)
    {
        $this->day = $day;
        $this->from = $from;
        $this->to = $to;
    }
}
