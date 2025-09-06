<?php

declare(strict_types=1);

namespace GeoIp\Contracts;

use GeoIp\Location;

interface Service
{
    /**
     * Attempt to locate the geolocation of the Ip address.
     *
     * @throws \GeoIp\Exceptions\InvalidIpAddressException
     * @throws \GeoIp\Exceptions\LocationNotFoundException
     * @throws \GeoIp\Exceptions\ServiceFailedException
     */
    public function locate(string $ip): Location;
}
