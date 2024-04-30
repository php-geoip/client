<?php

declare(strict_types=1);

namespace GeoIp\Tests\Unit;

use GeoIp\Exceptions\InvalidIpAddressException;
use GeoIp\GeoIp;
use GeoIp\Location;
use GeoIp\Tests\Mocks\MockService;
use GeoIp\Caches\NullCache;
use PHPUnit\Framework\TestCase;

class GeoIpTest extends TestCase
{
    /** @dataProvider providesValidIpAddresses */
    public function test_it_locates_an_ip_addresses_geolocation(string $ip): void
    {
        $geoip = new GeoIp(new MockService(), new NullCache());

        $location = $geoip->locate($ip);

        $this->assertEquals($ip, $location->ip);
    }

    /** @dataProvider providesInvalidIpAddresses */
    public function test_it_requires_a_valid_ip_address(string $ip): void
    {
        $geoip = new GeoIp(new MockService(), new NullCache());

        $this->expectException(InvalidIpAddressException::class);

        $geoip->locate($ip);
    }

    /** @dataProvider providesPrivateIpAddresses */
    public function test_it_requires_a_public_ip_address(string $ip): void
    {
        $geoip = new GeoIp(new MockService(), new NullCache());

        $this->expectException(InvalidIpAddressException::class);

        $geoip->locate($ip);
    }

    /** @dataProvider providesPrivateIpAddresses */
    public function test_it_falls_back_to_a_default_location_if_a_private_ip_address_is_provided(string $ip): void
    {
        $geoip = new GeoIp(
            new MockService(),
            new NullCache(),
            $default = new Location('1.1.1.1', 'US', 'United States')
        );

        $location = $geoip->locate($ip);

        $this->assertNotEquals($location->ip, $ip);
        $this->assertEquals($default->ip, $location->ip);
    }

    public function test_it_adds_a_currency_if_one_exists(): void
    {
        $geoip = new GeoIp(new MockService(), new NullCache());
        $location = $geoip->locate('1.1.1.1');
        $this->assertEquals('1.1.1.1', $location->ip);
    }

    /**
     * @return array<string, array<string>>
     */
    public static function providesValidIpAddresses(): array
    {
        return [
            'ipv4' => ['1.1.1.1'],
            'ipv6' => ['2606:4700:4700::1111'],
        ];
    }

    /**
     * @return array<string, array<string>>
     */
    public static function providesInvalidIpAddresses(): array
    {
        return [
            'ipv4' => ['1.1.1.1111'],
            'ipv6' => ['fe80:2030:31:24'],
        ];
    }

    /**
     * @return array<string, array<string>>
     */
    public static function providesPrivateIpAddresses(): array
    {
        return [
            'ipv4' => ['127.0.0.1'],
            'ipv6' => ['FDC8:BF8B:E62C:ABCD:1111:2222:3333:4444'],
        ];
    }
}
