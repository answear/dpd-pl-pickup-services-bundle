<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Type;

class PUDO
{
    public string $id;
    public bool $active;
    public Type $type;
    public string $language;
    public Address $address;
    public Coordinates $coordinates;
    public AdditionalInfo $additionalInfo;
    public Week $opened;
    /**
     * @var HolidayDates[]
     */
    public array $holidays = [];
}
