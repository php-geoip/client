<?php

declare(strict_types=1);

namespace GeoIp\Events;

use GeoIp\Contracts\GeoIpEvent;

readonly class CacheMiss implements GeoIpEvent
{
    private float $timestamp;

    public function __construct(public string $ip)
    {
        $this->timestamp = microtime(true);
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }
}
