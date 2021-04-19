<?php

declare(strict_types=1);

namespace Service;

use Answear\DpdPlPickupServicesBundle\DependencyInjection\Configuration;
use Answear\DpdPlPickupServicesBundle\Exception\MalformedResponseException;
use Answear\DpdPlPickupServicesBundle\Exception\ServiceException;
use Answear\DpdPlPickupServicesBundle\Service\ConfigProvider;
use Answear\DpdPlPickupServicesBundle\Service\PUDOFactory;
use Answear\DpdPlPickupServicesBundle\Service\PUDOList;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use Answear\DpdPlPickupServicesBundle\ValueObject\PUDO;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PUDOListTest extends TestCase
{
    /**
     * @see http://docs.guzzlephp.org/en/stable/testing.html#history-middleware
     */
    private array $guzzleHistory;
    private MockHandler $guzzleHandler;
    private PUDOList $PUDOList;

    public function setUp(): void
    {
        $this->guzzleHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->guzzleHandler);

        $this->guzzleHistory = [];
        $history = Middleware::history($this->guzzleHistory);
        $handlerStack->push($history);

        $this->PUDOList = new PUDOList(
            new PUDOFactory(),
            $this->createMock(ConfigProvider::class),
            new Client(
                [
                    'base_uri' => Configuration::API_URL,
                    'handler' => $handlerStack,
                    'http_errors' => false,
                ]
            )
        );
    }

    /**
     * @test
     */
    public function byAddress(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/three_results.xml')
            )
        );

        $this->assertThreeResults($this->PUDOList->byAddress('31-564', 'Kraków', 'Aleja Pokoju 18'));

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

    /**
     * @test
     */
    public function byCountry(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/three_results.xml')
            )
        );

        $this->assertThreeResults($this->PUDOList->byCountry('POL'));

        /** @var Request $request */
        $request = $this->guzzleHistory[0]['request'];
        self::assertSame('/api/pudo/list/bycountry', $request->getUri()->getPath());
        \parse_str($request->getUri()->getQuery(), $query);
        self::assertArrayHasKey('key', $query);
        self::assertSame('POL', $query['countryCode']);
    }

    /**
     * @test
     */
    public function byCountryNoResults(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/no_items.xml')
            )
        );

        self::assertCount(0, $this->PUDOList->byCountry('POL'));
    }

    /**
     * @test
     */
    public function byId(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/full_pudo.xml')
            )
        );

        $pudo = $this->PUDOList->byId('PL15625');
        self::assertInstanceOf(PUDO::class, $pudo);
        self::assertSame('PL15625', $pudo->id);

        /** @var Request $request */
        $request = $this->guzzleHistory[0]['request'];
        self::assertSame('/api/pudo/details', $request->getUri()->getPath());
        \parse_str($request->getUri()->getQuery(), $query);
        self::assertArrayHasKey('key', $query);
        self::assertSame('PL15625', $query['pudoId']);
    }

    /**
     * @test
     */
    public function byIdNoResult(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/no_items.xml')
            )
        );

        self::assertNull($this->PUDOList->byId('PL15625'));
    }

    /**
     * @test
     */
    public function byLatLng(): void
    {
        $this->guzzleHandler->append(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../fixtures/three_results.xml')
            )
        );

        $this->assertThreeResults($this->PUDOList->byLatLng(new Coordinates(50.061389, 19.937222), 100));

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

    /**
     * @test
     */
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

        $this->PUDOList->byId('PL15625');
    }

    /**
     * @test
     */
    public function noXmlInResponse(): void
    {
        $this->guzzleHandler->append(new Response(500, [], 'Internal Server Error'));

        try {
            $this->PUDOList->byId('PL15625');
            self::fail('An exception should have been thrown');
        } catch (MalformedResponseException $e) {
            self::assertSame('Internal Server Error', $e->getResponse());
        }
    }

    private function assertThreeResults(array $results): void
    {
        self::assertCount(3, $results);
        $expected = ['PL11011', 'PL11016', 'PL11021'];
        foreach ($results as $i => $result) {
            self::assertInstanceOf(PUDO::class, $result);
            self::assertSame($expected[$i], $result->id);
        }
    }
}
