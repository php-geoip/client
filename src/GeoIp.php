<?php

declare(strict_types=1);

namespace GeoIp;

use GeoIp\Caches\NullCache;
use GeoIp\Contracts\Cache;
use GeoIp\Contracts\Service;
use GeoIp\Exceptions\InvalidIpAddressException;
use GeoIp\Exceptions\LocationNotFoundException;

final readonly class GeoIp
{
    public function __construct(
        private Service $service,
        private Cache $cache = new NullCache(),
        private ?Location $default = null
    ) {
    }

    /**
     * @throws \GeoIp\Exceptions\InvalidIpAddressException
     * @throws \GeoIp\Exceptions\LocationNotFoundException
     */
    public function locate(string $ip): Location
    {
        try {
            if (! $this->isValid($ip)) {
                throw new InvalidIpAddressException($ip);
            }

            return $this->cache->remember($ip, fn ($ip) => $this->service->locate($ip));
        } catch (InvalidIpAddressException | LocationNotFoundException $e) {
            return $this->default ?? throw $e;
        }
    }

    private function isValid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE);
    }
}
