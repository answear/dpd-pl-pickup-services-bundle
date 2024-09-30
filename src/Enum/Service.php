<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Enum;

enum Service: string
{
    case Delivery = '100';
    case COD = '101';
    case Swap = '300';
    case DropoffOnline = '200';
    case DropoffOffline = '201';
    case MonitoredDocuments = 'P10';
    case Rod = 'P20';
    case Tyres = 'P30';
    case Pallet = 'P90';
    case DressingRoom = '10001';
}
