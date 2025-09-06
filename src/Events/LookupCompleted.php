<?php

declare(strict_types=1);

namespace GeoIp\Events;

use GeoIp\Contracts\GeoIpEvent;
use GeoIp\Contracts\Service;
use GeoIp\Location;

readonly class LookupCompleted implements GeoIpEvent
{
    private float $timestamp;

    public function __construct(
        public Service $service,
        public string $ip,
        public Location $location,
        public float $duration,
    ) {
        $this->timestamp = microtime(true);
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }
}
