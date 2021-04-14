<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\ValueObject;

use Answear\DpdPlPickupServicesBundle\Enum\Day;
use Webmozart\Assert\Assert;

class Week implements \IteratorAggregate
{
    private array $openingTimes = [];

    public function __construct(OpeningTime ...$openingTimes)
    {
        foreach ($openingTimes as $opening) {
            Assert::keyNotExists($this->openingTimes, $opening->day->getValue());
            $this->openingTimes[$opening->day->getValue()] = $opening;
        }
    }

    /**
     * @return \Traversable<Day, ?OpeningTime>
     */
    public function getIterator(): \Traversable
    {
        foreach (Day::getEnumerators() as $day) {
            yield $day => $this->openingTimes[$day->getValue()] ?? null;
        }
    }

    public function isOpened(Day $day): bool
    {
        return isset($this->openingTimes[$day->getValue()]);
    }
}
