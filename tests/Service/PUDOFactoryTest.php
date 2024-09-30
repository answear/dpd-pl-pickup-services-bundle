<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Tests\Service;

use Answear\DpdPlPickupServicesBundle\Enum\Day;
use Answear\DpdPlPickupServicesBundle\Enum\Service;
use Answear\DpdPlPickupServicesBundle\Enum\Type;
use Answear\DpdPlPickupServicesBundle\Service\PUDOFactory;
use Answear\DpdPlPickupServicesBundle\ValueObject\Coordinates;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PUDOFactoryTest extends TestCase
{
    private PUDOFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new PUDOFactory();
    }

    #[Test]
    public function dataIsCorrect(): void
    {
        $xml = simplexml_load_string(file_get_contents(__DIR__ . '/../fixtures/full_pudo.xml'));
        $pudo = $this->factory->fromXmlElement($xml->PUDO_ITEMS[0]->PUDO_ITEM);

        self::assertSame('PL15625', $pudo->id);
        self::assertTrue($pudo->active);
        self::assertSame(Type::Standard, $pudo->type);
        self::assertSame('PL', $pudo->language);
        self::assertSame('Na Szaniec 21 box 4', $pudo->address->address1);
        self::assertSame('ADDRESS2', $pudo->address->address2);
        self::assertSame('ADDRESS3', $pudo->address->address3);
        self::assertSame('Sklep Zoologiczny Akita : 885558055', $pudo->address->locationHint);
        self::assertSame('31564', $pudo->address->zipCode);
        self::assertSame('Kraków', $pudo->address->city);
        self::assertSame('POL', $pudo->address->country);
        self::assertEquals(new Coordinates(50.05874, 19.97894), $pudo->coordinates);
        self::assertCount(3, $pudo->additionalInfo->services);
        self::assertTrue($pudo->additionalInfo->hasService(Service::Delivery));
        self::assertTrue($pudo->additionalInfo->hasService(Service::DropoffOnline));
        self::assertTrue($pudo->additionalInfo->hasService(Service::DropoffOffline));
        self::assertFalse($pudo->additionalInfo->hasService(Service::Swap));
        self::assertTrue($pudo->additionalInfo->parking);
        self::assertFalse($pudo->additionalInfo->wheelchairAccessible);
        foreach ($pudo->opened as $day => $openings) {
            switch (true) {
                case Day::Sunday === $day:
                    self::assertCount(0, $openings);
                    break;
                case Day::Saturday === $day:
                    self::assertCount(2, $openings);
                    self::assertSame('10:00', $openings[0]->from);
                    self::assertSame('13:00', $openings[0]->to);
                    self::assertSame('18:00', $openings[1]->from);
                    self::assertSame('20:00', $openings[1]->to);
                    break;
                default:
                    self::assertCount(1, $openings);
                    self::assertSame('11:00', $openings[0]->from);
                    self::assertSame('17:00', $openings[0]->to);
                    break;
            }
        }
        self::assertCount(1, $pudo->holidays);
        self::assertEquals(new \DateTimeImmutable('06.04.2021 00:00:00'), $pudo->holidays[0]->from);
        self::assertEquals(new \DateTimeImmutable('18.04.2021 23:59:59'), $pudo->holidays[0]->to);
    }
}
