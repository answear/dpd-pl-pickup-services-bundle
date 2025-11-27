<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

use Answear\DpdPlPickupServicesBundle\Exception\MalformedResponseException;
use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

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
                RequestOptions::TIMEOUT => $configProvider->requestTimeout,
            ]
        );
    }

    /**
     * @return iterable<PUDO>
     */
    protected function request(string $endpoint, array $params): iterable
    {
        $response = $this->query($endpoint, $params);

        $localFile = null;
        try {
            $localFile = tempnam(sys_get_temp_dir(), 'DPD_pp_');
            $this->saveToLocalFile($localFile, $response);

            $reader = \XMLReader::open($localFile);

            while ($reader->read()) {
                if (\XMLReader::ELEMENT === $reader->nodeType && 'ERROR' === $reader->name) {
                    $error = new \SimpleXMLElement($reader->readOuterXML());
                    if ($error && isset($error->VALUE)) {
                        throw new ServiceException((string) $error->VALUE, (int) $error['code']);
                    }
                }

                if (\XMLReader::ELEMENT === $reader->nodeType && 'PUDO_ITEM' === $reader->name) {
                    $pudoElement = new \SimpleXMLElement($reader->readOuterXML());

                    yield $this->PUDOFactory->fromXmlElement($pudoElement);
                }
            }

            $reader->close();
        } finally {
            $this->deleteLocalFile($localFile);
        }
    }

    private function query(string $endpoint, array $params): ResponseInterface
    {
        $params['key'] = $this->configProvider->key;
        $response = null;
        try {
            $response = $this->client->request(
                'GET',
                $endpoint,
                [
                    RequestOptions::QUERY => $params,
                    RequestOptions::STREAM => true,
                    RequestOptions::HTTP_ERRORS => true,
                ]
            );
        } catch (ServerException $exception) {
            throw new MalformedResponseException($exception->getMessage(), $exception);
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        } catch (\Throwable) {
        }

        if (null === $response) {
            throw new MalformedResponseException('Empty response.');
        }

        return $response;
    }

    private function saveToLocalFile(string $localFile, ResponseInterface $response): void
    {
        $fileHandle = fopen($localFile, 'wb');

        $stream = $response->getBody();
        $chunkSize = 8192;
        while (!$stream->eof()) {
            $chunk = $stream->read($chunkSize);
            if (empty($chunk)) {
                break;
            }
            fwrite($fileHandle, $chunk);
        }

        fclose($fileHandle);

        if (filesize($localFile) <= 0) {
            throw new MalformedResponseException('Empty local xml file after request.');
        }
    }

    private function deleteLocalFile(mixed $localFile): void
    {
        if (!is_string($localFile) || !file_exists($localFile)) {
            return;
        }

        try {
            unlink($localFile);
        } catch (\Throwable) {
        }
    }
}
