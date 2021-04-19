<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Tests\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Day;
use Answear\DpdPlPickupServicesBundle\ValueObject\OpeningTime;
use Answear\DpdPlPickupServicesBundle\ValueObject\WeekStoreHours;
use PHPUnit\Framework\TestCase;

class WeekStoreHoursTest extends TestCase
{
    /**
     * @test
     */
    public function daysAreInCorrectOrder(): void
    {
        $expected = [
            Day::monday(),
            Day::tuesday(),
            Day::wednesday(),
            Day::thursday(),
            Day::friday(),
            Day::saturday(),
            Day::sunday(),
        ];
        $week = new WeekStoreHours(
            new OpeningTime(Day::sunday(), '10:00', '11:00'),
            new OpeningTime(Day::sunday(), '12:00', '14:00'),
            new OpeningTime(Day::wednesday(), '10:00', '11:00'),
        );

        $i = 0;
        foreach ($week as $day => $opening) {
            self::assertTrue($expected[$i++]->is($day));
        }
    }

    /**
     * @test
     */
    public function isOpened(): void
    {
        $week = new WeekStoreHours(new OpeningTime(Day::monday(), '10:00', '11:00'));
        self::assertTrue($week->isOpened(Day::monday()));
        self::assertFalse($week->isOpened(Day::tuesday()));
    }
}
