<?php

declare(strict_types=1);

namespace ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Day;
use Answear\DpdPlPickupServicesBundle\ValueObject\OpeningTime;
use Answear\DpdPlPickupServicesBundle\ValueObject\Week;
use PHPUnit\Framework\TestCase;

class WeekTest extends TestCase
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
        $week = new Week(
            new OpeningTime(Day::sunday(), '10:00', '11:00'),
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
    public function duplicatedDayResultsInException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Week(
            new OpeningTime(Day::monday(), '10:00', '11:00'),
            new OpeningTime(Day::monday(), '10:00', '11:00'),
        );
    }

    /**
     * @test
     */
    public function isOpened(): void
    {
        $week = new Week(new OpeningTime(Day::monday(), '10:00', '11:00'));
        self::assertTrue($week->isOpened(Day::monday()));
        self::assertFalse($week->isOpened(Day::tuesday()));
    }
}
