<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Service;

class AdditionalInfo
{
    /**
     * @var Service[]
     */
    public array $services = [];
    public bool $wheelchairAccessible;
    public bool $parking;

    public function hasService(Service $service): bool
    {
        return \in_array($service, $this->services, true);
    }
}
