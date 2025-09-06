<?php

declare(strict_types=1);

namespace GeoIp\Exceptions;

class InvalidIpAddressException extends GeoIpException
{
    public function __construct(string $ip)
    {
        parent::__construct("Must provide a valid Ip address.");
    }
}
