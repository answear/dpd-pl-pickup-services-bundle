<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\Exception\MalformedResponseException;
use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class PUDOListStreaming extends AbstractPUDOList
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
     */
    protected function request(string $endpoint, array $params): iterable
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
