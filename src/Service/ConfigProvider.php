<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

class ConfigProvider
{
    private string $url;
    private string $key;
    private float $requestTimeout;

    public function __construct(string $url, string $key, float $requestTimeout)
    {
        $this->url = $url;
        $this->key = $key;
        $this->requestTimeout = $requestTimeout;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getRequestTimeout(): float
    {
        return $this->requestTimeout;
    }
}
