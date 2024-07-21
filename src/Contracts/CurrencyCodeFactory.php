<?php

declare(strict_types=1);

namespace GeoIp\Contracts;

use GeoIp\Location;

interface CurrencyCodeFactory
{
    public function forLocation(Location $location): ?string;
}
