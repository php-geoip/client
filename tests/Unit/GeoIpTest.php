<?php

declare(strict_types=1);

namespace GeoIp\Tests\Unit;

use GeoIp\Exceptions\InvalidIpAddressException;
use GeoIp\GeoIp;
use GeoIp\Location;
use GeoIp\Tests\Mocks\MockService;
use GeoIp\Caches\NullCache;
use GeoIp\Caches\MemoryCache;
use GeoIp\SimpleEventDispatcher;
use GeoIp\Events\CacheHit;
use GeoIp\Events\CacheMiss;
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
            default: $default = new Location('1.1.1.1', 'US', 'United States')
        );

        $location = $geoip->locate($ip);

        $this->assertNotEquals($location->ip, $ip);
        $this->assertEquals($default->ip, $location->ip);
    }

    public function test_it_fires_cache_miss_event_on_first_lookup(): void
    {
        $dispatcher = new SimpleEventDispatcher();
        $events = [];

        $dispatcher->listen(CacheMiss::class, function (CacheMiss $event) use (&$events) {
            $events[] = $event;
        });

        $geoip = new GeoIp(new MockService(), new MemoryCache(), null, $dispatcher);
        $geoip->locate('1.1.1.1');

        $this->assertCount(1, $events);
        $this->assertInstanceOf(CacheMiss::class, $events[0]);
        $this->assertEquals('1.1.1.1', $events[0]->ip);
        $this->assertIsFloat($events[0]->getTimestamp());
    }

    public function test_it_fires_cache_hit_event_on_subsequent_lookups(): void
    {
        $dispatcher = new SimpleEventDispatcher();
        $cacheHits = [];
        $cacheMisses = [];

        $dispatcher->listen(CacheHit::class, function (CacheHit $event) use (&$cacheHits) {
            $cacheHits[] = $event;
        });

        $dispatcher->listen(CacheMiss::class, function (CacheMiss $event) use (&$cacheMisses) {
            $cacheMisses[] = $event;
        });

        $geoip = new GeoIp(new MockService(), new MemoryCache(), null, $dispatcher);

        // First lookup - should be a cache miss
        $firstLocation = $geoip->locate('1.1.1.1');

        // Second lookup of same IP - should be a cache hit
        $secondLocation = $geoip->locate('1.1.1.1');

        // Verify cache miss occurred on first lookup
        $this->assertCount(1, $cacheMisses);
        $this->assertEquals('1.1.1.1', $cacheMisses[0]->ip);

        // Verify cache hit occurred on second lookup
        $this->assertCount(1, $cacheHits);
        $this->assertEquals('1.1.1.1', $cacheHits[0]->ip);
        $this->assertEquals($firstLocation, $cacheHits[0]->location);
        $this->assertIsFloat($cacheHits[0]->getTimestamp());

        // Verify both lookups returned the same location
        $this->assertEquals($firstLocation->ip, $secondLocation->ip);
    }

    public function test_it_fires_cache_miss_for_different_ips(): void
    {
        $dispatcher = new SimpleEventDispatcher();
        $events = [];

        $dispatcher->listen(CacheMiss::class, function (CacheMiss $event) use (&$events) {
            $events[] = $event;
        });

        $geoip = new GeoIp(new MockService(), new MemoryCache(), null, $dispatcher);

        $geoip->locate('1.1.1.1');
        $geoip->locate('8.8.8.8');

        $this->assertCount(2, $events);
        $this->assertEquals('1.1.1.1', $events[0]->ip);
        $this->assertEquals('8.8.8.8', $events[1]->ip);
    }

    public function test_it_does_not_fire_cache_hit_events_with_null_cache(): void
    {
        $dispatcher = new SimpleEventDispatcher();
        $cacheEvents = [];

        $dispatcher->listen(CacheHit::class, function (CacheHit $event) use (&$cacheEvents) {
            $cacheEvents[] = ['type' => 'hit', 'event' => $event];
        });

        $dispatcher->listen(CacheMiss::class, function (CacheMiss $event) use (&$cacheEvents) {
            $cacheEvents[] = ['type' => 'miss', 'event' => $event];
        });

        $geoip = new GeoIp(new MockService(), new NullCache(), null, $dispatcher);

        // Multiple calls to same IP
        $geoip->locate('1.1.1.1');
        $geoip->locate('1.1.1.1');

        // NullCache should result in cache miss events for every call
        $this->assertCount(2, $cacheEvents);
        $this->assertEquals('miss', $cacheEvents[0]['type']);
        $this->assertEquals('miss', $cacheEvents[1]['type']);
    }

    public function test_cache_events_contain_expected_data(): void
    {
        $dispatcher = new SimpleEventDispatcher();
        $cacheHit = null;
        $cacheMiss = null;

        $dispatcher->listen(CacheHit::class, function (CacheHit $event) use (&$cacheHit) {
            $cacheHit = $event;
        });

        $dispatcher->listen(CacheMiss::class, function (CacheMiss $event) use (&$cacheMiss) {
            $cacheMiss = $event;
        });

        $geoip = new GeoIp(new MockService(), new MemoryCache(), null, $dispatcher);

        $geoip->locate('1.1.1.1'); // Miss
        $geoip->locate('1.1.1.1'); // Hit

        // Verify cache miss event
        $this->assertNotNull($cacheMiss);
        $this->assertEquals('1.1.1.1', $cacheMiss->ip);
        $this->assertGreaterThan(0, $cacheMiss->getTimestamp());

        // Verify cache hit event
        $this->assertNotNull($cacheHit);
        $this->assertEquals('1.1.1.1', $cacheHit->ip);
        $this->assertInstanceOf(Location::class, $cacheHit->location);
        $this->assertEquals('1.1.1.1', $cacheHit->location->ip);
        $this->assertGreaterThan(0, $cacheHit->getTimestamp());
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
