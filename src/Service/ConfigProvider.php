<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\Service;

class ConfigProvider
{
    public string $url;

    public function __construct(
        string $url,
        public string $key,
        public float $requestTimeout,
    ) {
        $this->url = rtrim($url, '/') . '/';
    }
}
