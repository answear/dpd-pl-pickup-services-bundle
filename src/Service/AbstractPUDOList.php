<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;

abstract class AbstractPUDOList
{
    /**
     * @return iterable<PUDO>
     *
     * @throws ServiceException
     */
    public function byAddress(string $zipCode, string $city, ?string $address = null): iterable
    {
        $params = [
            'requestID' => \uniqid('', true),
            'city' => $city,
            'zipCode' => $zipCode,
            'servicePudo_display' => 1,
        ];
        if (null !== $address) {
            $params['address'] = $address;
        }

        return $this->request('list/byaddress', $params);
    }

    /**
     * @return iterable<PUDO>
     *
     * @throws ServiceException
     */
    public function byCountry(string $countryCode): iterable
    {
        return $this->request('list/bycountry', ['countryCode' => $countryCode]);
    }

    /**
     * @throws ServiceException
     */
    public function byId(string $id): ?PUDO
    {
        $generator = $this->request('details', ['pudoId' => $id]);

        foreach ($generator as $pudo) {
            return $pudo;
        }

        return null;
    }

    /**
     * @return iterable<PUDO>
     *
     * @throws ServiceException
     */
    public function byLatLng(Coordinates $coordinates, int $distance): iterable
    {
        return $this->request(
            'list/bylonglat',
            [
                'requestID' => \uniqid('', true),
                'latitude' => $coordinates->latitude,
                'longitude' => $coordinates->longitude,
                'max_distance_search' => $distance,
            ]
        );
    }

    
    abstract protected function request(string $endpoint, array $params): iterable;
}
