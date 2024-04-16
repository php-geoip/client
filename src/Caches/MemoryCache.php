<?php

declare(strict_types=1);

namespace GeoIp\Caches;

use Closure;
use GeoIp\Contracts\Cache;
use GeoIp\Location;

final class MemoryCache implements Cache
{
    /**
     * @var array<string, Location>
     */
    private array $cache = [];

    public function remember(string $ip, Closure $closure): Location
    {
        return $this->cache[$ip] ??= $closure($ip);
    }
}
