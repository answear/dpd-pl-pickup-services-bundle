<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\Enum\Day;
use Answear\DpdPlPickupServicesBundle\Enum\Service;
use Answear\DpdPlPickupServicesBundle\Enum\Type;
use Answear\DpdPlPickupServicesBundle\ValueObject\AdditionalInfo;
use Answear\DpdPlPickupServicesBundle\ValueObject\Address;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\HolidayDates;
use Answear\DpdPlPickupServicesBundle\ValueObject\OpeningTime;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use Answear\DpdPlPickupServicesBundle\ValueObject\WeekStoreHours;

class PUDOFactory
{
    public function fromXmlElement(\SimpleXMLElement $xml): PUDO
    {
        $pudo = new PUDO();
        $pudo->active = 'true' === (string) $xml['active'];
        $pudo->id = (string) $xml->PUDO_ID;
        $pudo->name = (string) $xml->NAME;
        $pudo->distance = (int)$xml->DISTANCE;
        $pudo->type = $this->getType($xml->PUDO_TYPE);
        $pudo->language = (string) $xml->LANGUAGE;
        $pudo->address = $this->createAddress($xml);
        $pudo->coordinates = new Coordinates($this->transformToFloat($xml->LATITUDE), $this->transformToFloat($xml->LONGITUDE));
        $pudo->additionalInfo = $this->createAdditionalInfo($xml);
        $pudo->opened = $this->createOpeningTimes($xml);
        $pudo->holidays = $this->createHolidays($xml);

        return $pudo;
    }

    private function createAddress(\SimpleXMLElement $xml): Address
    {
        $address = new Address();
        $address->address1 = (string) $xml->ADDRESS1;
        $address->address2 = (string) $xml->ADDRESS2;
        $address->address3 = (string) $xml->ADDRESS3;
        $address->locationHint = (string) $xml->LOCATION_HINT;
        $address->zipCode = (string) $xml->ZIPCODE;
        $address->city = (string) $xml->CITY;
        $address->country = (string) $xml->COUNTRY;

        return $address;
    }

    private function createAdditionalInfo(\SimpleXMLElement $xml): AdditionalInfo
    {
        $info = new AdditionalInfo();
        foreach (explode(';', (string) $xml->SERVICE_PUDO) as $service) {
            try {
                $info->services[] = Service::byValue($service);
            } catch (\InvalidArgumentException $e) {
                // NOP, do not fail hard for new services
            }
        }
        $info->wheelchairAccessible = 'true' === (string) $xml->HANDICAPE;
        $info->parking = 'true' === (string) $xml->PARKING;

        return $info;
    }

    private function createOpeningTimes(\SimpleXMLElement $xml): WeekStoreHours
    {
        $openings = [];
        foreach ($xml->OPENING_HOURS_ITEMS->OPENING_HOURS_ITEM as $opening) {
            $openings[] = new OpeningTime(
                Day::byValue((string) $opening->DAY_ID),
                (string) $opening->START_TM,
                (string) $opening->END_TM
            );
        }

        return new WeekStoreHours(...$openings);
    }

    /**
     * @return HolidayDates[]
     */
    private function createHolidays(\SimpleXMLElement $xml): array
    {
        $dates = [];
        foreach ($xml->HOLIDAY_ITEMS->HOLIDAY_ITEM as $holiday) {
            $start = $holiday->START_TM ?? $holiday->START_DTM ?? null;
            $end = $holiday->END_TM ?? $holiday->END_DTM ?? null;

            $dates[] = new HolidayDates(
                \DateTimeImmutable::createFromFormat('d/m/Y', (string) $start)->setTime(0, 0, 0),
                \DateTimeImmutable::createFromFormat('d/m/Y', (string) $end)->setTime(23, 59, 59)
            );
        }

        return $dates;
    }

    private function getType(\SimpleXMLElement $xml_val): Type
    {
        $val = 0 !== $xml_val->count() ? $xml_val : Type::IF_EMPTY;
        return Type::byValue((string) $val);
    }

    private function transformToFloat(\SimpleXMLElement $xml_val): float
    {
        return (float) str_replace(',', '.', $xml_val);
    }
}
