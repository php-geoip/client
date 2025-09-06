<?php

declare(strict_types=1);

namespace GeoIp;

use GeoIp\Caches\NullCache;
use GeoIp\Contracts\Cache;
use GeoIp\Contracts\Service;
use GeoIp\Events\CacheHit;
use GeoIp\Events\CacheMiss;
use GeoIp\Events\LookupCompleted;
use GeoIp\Events\LookupFailed;
use GeoIp\Events\LookupStarted;
use GeoIp\Exceptions\InvalidIpAddressException;
use GeoIp\Exceptions\LocationNotFoundException;
use GeoIp\Exceptions\ServiceFailedException;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class GeoIp
{
    public function __construct(
        private Service $service,
        private Cache $cache = new NullCache(),
        private ?Location $default = null,
        private ?EventDispatcherInterface $events = null,
    ) {
    }

    /**
     * @throws \GeoIp\Exceptions\InvalidIpAddressException
     * @throws \GeoIp\Exceptions\LocationNotFoundException
     * @throws \GeoIp\Exceptions\ServiceFailedException
     */
    public function locate(string $ip): Location
    {
        $start = microtime(true);
        $this->events?->dispatch(new LookupStarted($this->service, $ip));

        try {
            if (! $this->isValid($ip)) {
                throw new InvalidIpAddressException($ip);
            }

            $cacheHit = true;

            $location = $this->cache->remember($ip, function ($ip) use (&$cacheHit) {
                $this->events?->dispatch(new CacheMiss($ip));
                $cacheHit = false;

                return $this->service->locate($ip);
            });

            if ($cacheHit) {
                $this->events?->dispatch(new CacheHit($ip, $location));
            }

            $this->events?->dispatch(new LookupCompleted($this->service, $ip, $location, microtime(true) - $start));

            return $location;
        } catch (InvalidIpAddressException | LocationNotFoundException | ServiceFailedException $e) {
            $this->events?->dispatch(new LookupFailed($this->service, $ip, $e, microtime(true) - $start));

            return $this->default ?? throw $e;
        }
    }

    private function isValid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE);
    }
}
