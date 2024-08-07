<?php

declare(strict_types=1);

namespace GeoIp\Contracts;

use Closure;
use GeoIp\Location;

interface Cache
{
    /**
     * @param Closure(string): Location $closure
     */
    public function remember(string $ip, Closure $closure): Location;
}
