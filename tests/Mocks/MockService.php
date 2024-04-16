<?php

declare(strict_types=1);

namespace GeoIp\Tests\Mocks;

use GeoIp\Contracts\Service;
use GeoIp\Location;

class MockService implements Service
{
    public function locate(string $ip): Location
    {
        return new Location($ip);
    }
}
