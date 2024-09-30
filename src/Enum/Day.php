<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

enum Day: string
{
    case Monday = '1';
    case Tuesday = '2';
    case Wednesday = '3';
    case Thursday = '4';
    case Friday = '5';
    case Saturday = '6';
    case Sunday = '7';
}
