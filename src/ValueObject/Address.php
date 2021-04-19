<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

class Address
{
    public ?string $address1;
    public ?string $address2;
    public ?string $address3;
    public string $locationHint;
    public string $zipCode;
    public string $city;
    public string $country;

    public function getFullAddress(): string
    {
        return $this->address1 . $this->address2 . $this->address3;
    }
}
