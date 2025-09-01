<?php

declare(strict_types=1);

namespace GeoIp\Events;

use Exception;
use GeoIp\Contracts\GeoIpEvent;
use GeoIp\Contracts\Service;

readonly class LookupFailed implements GeoIpEvent
{
    private float $timestamp;

    public function __construct(
        public Service $service,
        public string $ip,
        public Exception $exception,
        public float $duration,
    ) {
        $this->timestamp = microtime(true);
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }
}
