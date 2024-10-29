<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

enum Type: string
{
    case Standard = '100';
    case Chain = '200';
    case Dpd = '300';
    case SwipBox1 = '400401';
    case SwipBox2 = '400402';
    case PointPack = '500501';
    case ForeignLocker = '400';
    case ForeignExternalLocker = '501';
    case ZabkaPickup = '500502';
}
