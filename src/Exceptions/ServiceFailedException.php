<?php

namespace GeoIp\Exceptions;

use Exception;
use GeoIp\Contracts\Service;

class ServiceFailedException extends GeoIpException
{
    public function __construct(Service $service, string $ip, ?Exception $previous = null)
    {
        parent::__construct("Service [" . get_class($service) . "] failed attempting to locate Ip address.", previous: $previous);
    }
}
