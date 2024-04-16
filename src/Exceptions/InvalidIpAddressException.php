<?php

declare(strict_types=1);

namespace GeoIp\Exceptions;

use InvalidArgumentException;

class InvalidIpAddressException extends InvalidArgumentException
{
    public function __construct(string $ip)
    {
        parent::__construct("Must provide a valid Ip address [$ip].");
    }
}
