<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Exception;

class MalformedResponseException extends \RuntimeException
{
    private string $response;

    public function __construct(string $response)
    {
        parent::__construct('Response is not a valid XML');
        $this->response = $response;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}
