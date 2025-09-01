<?php

declare(strict_types=1);

namespace GeoIp\Contracts;

interface GeoIpEvent
{
    public function getTimestamp(): float;
}
