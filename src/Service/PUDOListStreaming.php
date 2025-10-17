<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\Exception\MalformedResponseException;
use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class PUDOListStreaming
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
                'stream' => true,
            ]
        );
    }

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

    /**
     * @return iterable<PUDO>
     */
    private function request(string $endpoint, array $params): iterable
    {
        $params['key'] = $this->configProvider->key;

        $response = $this->client->request('GET', $endpoint, ['query' => $params]);
        $stream = $response->getBody();

        $xmlContent = '';
        while (!$stream->eof()) {
            $xmlContent .= $stream->read(8192);
        }

        $xml = @\simplexml_load_string($xmlContent);
        if (false === $xml) {
            throw new MalformedResponseException($xmlContent);
        }

        if ($xml->ERROR) {
            throw new ServiceException((string) $xml->ERROR->VALUE, (int) $xml->ERROR['code']);
        }

        $reader = new \XMLReader();
        $reader->XML($xmlContent);

        while ($reader->read()) {
            if ($reader->nodeType === \XMLReader::ELEMENT && $reader->name === 'PUDO_ITEM') {
                $pudoXml = $reader->readOuterXml();
                $pudoElement = @\simplexml_load_string($pudoXml);

                if (false !== $pudoElement) {
                    yield $this->PUDOFactory->fromXmlElement($pudoElement);
                }
            }
        }

        $reader->close();
    }
}
