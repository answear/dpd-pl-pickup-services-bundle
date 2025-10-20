<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\Exception\MalformedResponseException;
use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class PUDOList extends AbstractPUDOList
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
     */
    protected function request(string $endpoint, array $params): iterable
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
