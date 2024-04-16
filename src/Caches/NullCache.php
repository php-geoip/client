<?php

declare(strict_types=1);

namespace GeoIp\Caches;

use Closure;
use GeoIp\Contracts\Cache;
use GeoIp\Location;

final class NullCache implements Cache
{
    public function remember(string $ip, Closure $closure): Location
    {
        return $closure($ip);
    }
}
