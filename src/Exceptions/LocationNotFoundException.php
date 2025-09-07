<?php

declare(strict_types=1);

namespace GeoIp\Exceptions;

class LocationNotFoundException extends GeoIpException
{
    public function __construct(string $ip)
    {
        parent::__construct("Unable to locate Ip address [$ip].");
    }
}
