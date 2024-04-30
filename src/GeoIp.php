<?php

declare(strict_types=1);

namespace GeoIp;

use GeoIp\Contracts\Cache;
use GeoIp\Contracts\Service;
use GeoIp\Exceptions\InvalidIpAddressException;
use GeoIp\Exceptions\LocationNotFoundException;
use GeoIp\Support\Currency;

final readonly class GeoIp
{
    private Location|null $default;

    public function __construct(private Service $service, private Cache $cache, Location|null $default = null)
    {
        $this->setDefaultLocation($default);
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

            return $this->cache->remember($ip, function ($ip) {
                $location = $this->service->locate($ip);

                if ($this->needsCurrency($location) && $currency = $this->getCurrency($location)) {
                    $location = $location->clone(currency: $currency);
                }

                return $location;
            });
        } catch (InvalidIpAddressException | LocationNotFoundException $e) {
            if ($this->default) {
                return $this->default;
            }

            throw $e;
        }
    }

    private function isValid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE);
    }

    private function setDefaultLocation(Location|null $default): void
    {
        $this->default = $default?->clone(isDefault: true);
    }

    private function needsCurrency(Location $location): bool
    {
        return $location->countryCode && ! $location->currency;
    }

    private function getCurrency(Location $location): string|null
    {
        return Currency::fromCountryCode($location->countryCode);
    }
}
