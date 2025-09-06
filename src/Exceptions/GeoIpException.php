<?php

declare(strict_types=1);

namespace GeoIp\Exceptions;

use Exception;
use GeoIp\Contracts\GeoIpException as GeoIpExceptionContract;

abstract class GeoIpException extends Exception implements GeoIpExceptionContract
{
}
