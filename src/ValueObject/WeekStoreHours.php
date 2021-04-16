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
            if (!isset($this->openingHours[$opening->day->getValue()])) {
                $this->openingHours[$opening->day->getValue()] = [];
            }
            $this->openingHours[$opening->day->getValue()][] = $opening;
        }
    }

    /**
     * @return \Traversable<Day, OpeningTime[]>
     */
    public function getIterator(): \Traversable
    {
        foreach (Day::getEnumerators() as $day) {
            yield $day => $this->openingHours[$day->getValue()] ?? [];
        }
    }

    public function isOpened(Day $day): bool
    {
        return isset($this->openingHours[$day->getValue()]);
    }
}
