<?php

declare(strict_types=1);

namespace GeoIp\Tests\Unit;

use GeoIp\Location;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function test_it_keeps_properties_when_cloned(): void
    {
        $location = new Location('1.1.1.1', 'US');

        $cloned = $location->clone();

        $this->assertEquals($location->countryCode, $cloned->countryCode);
    }

    public function test_it_overwrites_passed_properties_when_cloned(): void
    {
        $location = new Location('1.1.1.1', 'US');

        $cloned = $location->clone(countryCode: 'CA');

        $this->assertEquals('CA', $cloned->countryCode);
    }
}
