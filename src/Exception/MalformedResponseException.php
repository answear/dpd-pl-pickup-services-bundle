<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Exception;

class MalformedResponseException extends \RuntimeException
{
    public function __construct(public string $response)
    {
        parent::__construct('Response is not a valid XML');
    }
}
