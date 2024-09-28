<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Day;

class WeekStoreHours implements \IteratorAggregate
{
    /**
     * @var OpeningTime[][]
     */
    private array $openingHours = [];

    public function __construct(OpeningTime ...$openingTimes)
    {
        foreach ($openingTimes as $opening) {
            if (!isset($this->openingHours[$opening->day->value])) {
                $this->openingHours[$opening->day->value] = [];
            }
            $this->openingHours[$opening->day->value][] = $opening;
        }
    }

    /**
     * @return \Traversable<Day, OpeningTime[]>
     */
    public function getIterator(): \Traversable
    {
        foreach (Day::cases() as $day) {
            yield $day => $this->openingHours[$day->value] ?? [];
        }
    }

    public function isOpened(Day $day): bool
    {
        return isset($this->openingHours[$day->value]);
    }
}
