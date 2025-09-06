<?php

declare(strict_types=1);

namespace GeoIp\Events;

use GeoIp\Contracts\GeoIpEvent;
use GeoIp\Location;

readonly class CacheHit implements GeoIpEvent
{
    private float $timestamp;

    public function __construct(public string $ip, public Location $location)
    {
        $this->timestamp = microtime(true);
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }
}
