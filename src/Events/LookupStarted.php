<?php

declare(strict_types=1);

namespace GeoIp\Events;

use GeoIp\Contracts\GeoIpEvent;
use GeoIp\Contracts\Service;

readonly class LookupStarted implements GeoIpEvent
{
    private float $timestamp;

    public function __construct(
        public Service $service,
        public string $ip,
    ) {
        $this->timestamp = microtime(true);
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }
}
