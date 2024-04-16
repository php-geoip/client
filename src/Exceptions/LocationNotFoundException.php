<?php

declare(strict_types=1);

namespace GeoIp\Exceptions;

use Exception;

class LocationNotFoundException extends Exception
{
    public function __construct(string $ip)
    {
        parent::__construct("Unable to locate Ip address [$ip].");
    }
}
