<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Exception;

class MalformedResponseException extends \RuntimeException
{
    public function __construct(public string $response, ?\Throwable $previous = null)
    {
        parent::__construct('Response is not a valid XML', $previous?->getCode() ?? 0, $previous);
    }
}
