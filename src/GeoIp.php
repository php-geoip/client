<?php

declare(strict_types=1);

namespace GeoIp;

use GeoIp\Contracts\Service;
use GeoIp\Events\CacheHit;
use GeoIp\Events\CacheMiss;
use GeoIp\Events\LookupCompleted;
use GeoIp\Events\LookupFailed;
use GeoIp\Events\LookupStarted;
use GeoIp\Exceptions\GeoIpException;
use GeoIp\Exceptions\InvalidIpAddressException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;

final readonly class GeoIp
{
    public function __construct(
        private Service $service,
        private ?Location $default = null,
        private ?CacheInterface $cache = null,
        private ?EventDispatcherInterface $events = null,
    ) {
    }

    /**
     * @throws GeoIpException
     * @throws CacheException
     */
    public function locate(string $ip): Location
    {
        $start = microtime(true);
        $this->events?->dispatch(new LookupStarted($this->service, $ip));

        try {
            if (! $this->isValid($ip)) {
                throw new InvalidIpAddressException($ip);
            }

            $location = $this->remember($ip);

            $this->events?->dispatch(new LookupCompleted($this->service, $ip, $location, microtime(true) - $start));

            return $location;
        } catch (GeoIpException $exception) {
            $this->events?->dispatch(new LookupFailed($this->service, $ip, $exception, microtime(true) - $start));

            return $this->default ?? throw $exception;
        }
    }

    /**
     * @throws GeoIpException
     * @throws CacheException
     */
    private function remember(string $ip): Location
    {
        if ($location = $this->cache?->get($ip)) {
            /** @var Location $location */
            $this->events?->dispatch(new CacheHit($ip, $location));

            return $location;
        }

        $this->events?->dispatch(new CacheMiss($ip));

        $location = $this->service->locate($ip);

        $this->cache?->set($ip, $location);

        return $location;
    }

    private function isValid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE);
    }
}
