<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Tests\Service;

use Answear\DpdPlPickupServicesBundle\DependencyInjection\Configuration;
use Answear\DpdPlPickupServicesBundle\Exception\MalformedResponseException;
use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\Service\ConfigProvider;
use Answear\DpdPlPickupServicesBundle\Service\PUDOFactory;
use Answear\DpdPlPickupServicesBundle\Service\PUDOListStreaming;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PUDOListStreamingTest extends TestCase
{
    private array $guzzleHistory;
    private MockHandler $guzzleHandler;
    private PUDOListStreaming $PUDOListStreaming;

    public function setUp(): void
    {
        $this->guzzleHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->guzzleHandler);

        $this->guzzleHistory = [];
        $history = Middleware::history($this->guzzleHistory);
        $handlerStack->push($history);

        $this->PUDOListStreaming = new PUDOListStreaming(
            new PUDOFactory(),
            new ConfigProvider('key', 'url', 10),
            new Client(
                [
                    'base_uri' => Configuration::API_URL,
                    'handler' => $handlerStack,
                    'stream' => true,
                ]
            )
        );
    }

    #[Test]
    public function byAddress(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/three_results.xml')
            )
        );

        $results = $this->PUDOListStreaming->byAddress('31-564', 'Kraków', 'Aleja Pokoju 18');
        $this->assertThreeResults($results);

        /** @var Request $request */
        $request = $this->guzzleHistory[0]['request'];
        self::assertSame('/api/pudo/list/byaddress', $request->getUri()->getPath());
        \parse_str($request->getUri()->getQuery(), $query);
        self::assertArrayHasKey('requestID', $query);
        self::assertArrayHasKey('key', $query);
        self::assertSame('Kraków', $query['city']);
        self::assertSame('31-564', $query['zipCode']);
        self::assertSame('Aleja Pokoju 18', $query['address']);
        self::assertSame('1', $query['servicePudo_display']);
    }

    #[Test]
    public function byCountry(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/three_results.xml')
            )
        );

        $results = $this->PUDOListStreaming->byCountry('POL');
        $this->assertThreeResults($results);

        /** @var Request $request */
        $request = $this->guzzleHistory[0]['request'];
        self::assertSame('/api/pudo/list/bycountry', $request->getUri()->getPath());
        \parse_str($request->getUri()->getQuery(), $query);
        self::assertArrayHasKey('key', $query);
        self::assertSame('POL', $query['countryCode']);
    }

    #[Test]
    public function byCountryNoResults(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/no_items.xml')
            )
        );

        $results = iterator_to_array($this->PUDOListStreaming->byCountry('POL'));
        self::assertCount(0, $results);
    }

    #[Test]
    public function byId(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/full_pudo.xml')
            )
        );

        $pudo = $this->PUDOListStreaming->byId('PL15625');
        self::assertInstanceOf(PUDO::class, $pudo);
        self::assertSame('PL15625', $pudo->id);

        /** @var Request $request */
        $request = $this->guzzleHistory[0]['request'];
        self::assertSame('/api/pudo/details', $request->getUri()->getPath());
        \parse_str($request->getUri()->getQuery(), $query);
        self::assertArrayHasKey('key', $query);
        self::assertSame('PL15625', $query['pudoId']);
    }

    #[Test]
    public function byIdNoResult(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/no_items.xml')
            )
        );

        self::assertNull($this->PUDOListStreaming->byId('PL15625'));
    }

    #[Test]
    public function byLatLng(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/three_results.xml')
            )
        );

        $results = $this->PUDOListStreaming->byLatLng(new Coordinates(50.061389, 19.937222), 100);
        $this->assertThreeResults($results);

        /** @var Request $request */
        $request = $this->guzzleHistory[0]['request'];
        self::assertSame('/api/pudo/list/bylonglat', $request->getUri()->getPath());
        \parse_str($request->getUri()->getQuery(), $query);
        self::assertArrayHasKey('requestID', $query);
        self::assertArrayHasKey('key', $query);
        self::assertSame('50.061389', $query['latitude']);
        self::assertSame('19.937222', $query['longitude']);
        self::assertSame('100', $query['max_distance_search']);
    }

    #[Test]
    public function apiError(): void
    {
        $this->guzzleHandler->append(
            new Response(
                401,
                [],
                file_get_contents(__DIR__ . '/../fixtures/auth_error.xml')
            )
        );

        $this->expectException(ServiceException::class);
        $this->expectExceptionCode(314);
        $this->expectExceptionMessage('Podana wartość key jest niepoprawna. Klucz nie istnieje lub jest nieaktywny.');

        $this->PUDOListStreaming->byId('PL15625');
    }

    #[Test]
    public function noXmlInResponse(): void
    {
        $exception = new \Exception('Internal Server Error', 500);
        $this->guzzleHandler->append(new Response($exception->getCode(), [], $exception->getMessage()));

        $this->expectExceptionObject(new MalformedResponseException($exception->getMessage(), $exception));
        $this->PUDOListStreaming->byId('PL15625');
    }

    private function assertThreeResults(iterable $results): void
    {
        $resultsArray = iterator_to_array($results);
        self::assertCount(3, $resultsArray);
        $expected = ['PL11011', 'PL11016', 'PL11021'];
        foreach ($resultsArray as $i => $result) {
            self::assertInstanceOf(PUDO::class, $result);
            self::assertSame($expected[$i], $result->id);
        }
    }
}
