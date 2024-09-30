<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Tests\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Day;
use Answear\DpdPlPickupServicesBundle\ValueObject\OpeningTime;
use Answear\DpdPlPickupServicesBundle\ValueObject\WeekStoreHours;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WeekStoreHoursTest extends TestCase
{
    #[Test]
    public function daysAreInCorrectOrder(): void
    {
        $expected = [
            Day::Monday,
            Day::Tuesday,
            Day::Wednesday,
            Day::Thursday,
            Day::Friday,
            Day::Saturday,
            Day::Sunday,
        ];
        $week = new WeekStoreHours(
            new OpeningTime(Day::Sunday, '10:00', '11:00'),
            new OpeningTime(Day::Sunday, '12:00', '14:00'),
            new OpeningTime(Day::Wednesday, '10:00', '11:00'),
        );

        $i = 0;
        foreach ($week as $day => $opening) {
            self::assertSame($expected[$i++], $day);
        }
    }

    #[Test]
    public function isOpened(): void
    {
        $week = new WeekStoreHours(new OpeningTime(Day::Monday, '10:00', '11:00'));
        self::assertTrue($week->isOpened(Day::Monday));
        self::assertFalse($week->isOpened(Day::Tuesday));
    }
}
