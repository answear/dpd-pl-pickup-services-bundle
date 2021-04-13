<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class PUDOList
{
    private ConfigProvider $configProvider;
    private ClientInterface $client;

    public function __construct(ConfigProvider $configProvider, ?ClientInterface $client = null)
    {
        $this->configProvider = $configProvider;
        $this->client = $client ?? new Client(
                [
                    'base_uri' => $configProvider->getUrl(),
                    'http_errors' => false,
                    'timeout' => $configProvider->getRequestTimeout(),
                ]
            );
    }

    /**
     * @return PUDO[]
     *
     * @throws ServiceException
     */
    public function byAddress(string $zipCode, string $city, ?string $street = null): array
    {
        // @todo
    }

    /**
     * @return PUDO[]
     *
     * @throws ServiceException
     */
    public function byCountry(string $countryCode): array
    {
        // @todo
    }

    /**
     * @throws ServiceException
     */
    public function byId(string $id): PUDO
    {
        // @todo
    }

    /**
     * @return PUDO[]
     *
     * @throws ServiceException
     */
    public function byLatLng(float $latitude, float $longitude, float $distance): array
    {
        // @todo
    }
}
