<?php

declare(strict_types=1);

namespace GeoIp\Tests\Integration;

use GeoIp\GeoIp;
use GeoIp\Services\MaxMind;
use GeoIp2\Database\Reader;
use GeoIp\Caches\NullCache;
use PHPUnit\Framework\TestCase;

class MaxMindDatabaseTest extends TestCase
{
    public function test_it_locates_an_ip_addresses_geolocation(): void
    {
        $geoip = new GeoIp(
            new MaxMind(new Reader(__DIR__.'/../Fixtures/GeoLite2-City.mmdb')),
            new NullCache()
        );

        $location = $geoip->locate('8.8.8.8');

        $this->assertEquals('8.8.8.8', $location->ip);
    }
}
