<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

class HolidayDates
{
    public \DateTimeImmutable $from;
    public \DateTimeImmutable $to;

    public function __construct(\DateTimeImmutable $from, \DateTimeImmutable $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
}
