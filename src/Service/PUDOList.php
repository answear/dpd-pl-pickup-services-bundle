<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\Exception\MalformedResponseException;
use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class PUDOList
{
    private ClientInterface $client;

    public function __construct(
        private PUDOFactory $PUDOFactory,
        private ConfigProvider $configProvider,
        ?ClientInterface $client = null,
    ) {
        $this->client = $client ?? new Client(
            [
                'base_uri' => $configProvider->url,
                'http_errors' => false,
                'timeout' => $configProvider->requestTimeout,
            ]
        );
    }

    /**
     * @return PUDO[]
     *
     * @throws ServiceException
     */
    public function byAddress(string $zipCode, string $city, ?string $address = null): array
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
     * @return PUDO[]
     *
     * @throws ServiceException
     */
    public function byCountry(string $countryCode): array
    {
        return $this->request('list/bycountry', ['countryCode' => $countryCode]);
    }

    /**
     * @throws ServiceException
     */
    public function byId(string $id): ?PUDO
    {
        $result = $this->request('details', ['pudoId' => $id]);

        return 1 === \count($result) ? \reset($result) : null;
    }

    /**
     * @return PUDO[]
     *
     * @throws ServiceException
     */
    public function byLatLng(Coordinates $coordinates, int $distance): array
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

    /**
     * @return PUDO[]
     */
    private function request(string $endpoint, array $params): array
    {
        $params['key'] = $this->configProvider->key;

        $response = $this->client->request('GET', $endpoint, ['query' => $params]);
        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
        }
        $responseText = $response->getBody()->getContents();
        $xml = @\simplexml_load_string($responseText);
        if (false === $xml) {
            throw new MalformedResponseException($responseText);
        }
        if ($xml->ERROR) {
            throw new ServiceException((string) $xml->ERROR->VALUE, (int) $xml->ERROR['code']);
        }

        $items = [];
        foreach ($xml->PUDO_ITEMS->PUDO_ITEM as $pudo) {
            $items[] = $this->PUDOFactory->fromXmlElement($pudo);
        }

        return $items;
    }
}
