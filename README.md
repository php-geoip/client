# GeoIp Core

[![Latest Version on Packagist](https://img.shields.io/packagist/v/php-geoip/core.svg?style=flat-square)](https://packagist.org/packages/php-geoip/core)
[![Tests](https://github.com/php-geoip/core/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/php-geoip/core/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/php-geoip/core.svg?style=flat-square)](https://packagist.org/packages/php-geoip/core)

A clean, extensible library for IP geolocation services. This package provides the core interfaces and functionality for resolving geographic information from IP addresses, with support for caching and event dispatching.

## Installation

Install via Composer:

```bash
composer require php-geoip/core
```

## Basic Usage

```php
use GeoIp\GeoIp;

// Create with a service implementation and optional cache
$geoip = new GeoIp(
    service: $service,          // Your service implementation
    cache: $cache,              // Optional caching
    events: $eventDispatcher    // Optional event dispatcher
);

$location = $geoip->locate('8.8.8.8');

echo $location->countryCode; // 'US'
echo $location->countryName; // 'United States'
echo $location->city;        // 'Mountain View'
```

## Architecture

This core library is designed to work with separate service provider packages:

- **Core** (this package) - Core interfaces, caching, and event handling
- **Providers** - Actual geolocation implementations (MaxMind, IP2Location, etc.)
- **Bundles** - Laravel, Symfony, and framework-agnostic integrations

## Location Data

The `Location` object provides comprehensive geographic information:

```php
$location = $geoip->locate('8.8.8.8');

$location->ip;           // '8.8.8.8'
$location->countryCode;  // 'US'
$location->countryName;  // 'United States'
$location->stateCode;    // 'CA'
$location->stateName;    // 'California'
$location->city;         // 'Mountain View'
$location->postalCode;   // '94043'
$location->continent;    // 'North America'
$location->latitude;     // 37.4192
$location->longitude;    // -122.0574
$location->timezone;     // 'America/Los_Angeles'
```

## Caching

Optionally provide any PSR-16 compatible cache implementations:

```php
$geoip = new GeoIp($service, cache: $cache);
```

## Events

The library dispatches events throughout the lookup process:

- `LookupStarted` - When a lookup begins
- `CacheHit` / `CacheMiss` - Cache lookup results
- `LookupCompleted` - Successful lookup with timing data
- `LookupFailed` - Failed lookup with exception details

```php
use GeoIp\Events\LookupCompleted;

$dispatcher->listen(LookupCompleted::class, function (LookupCompleted $event) {
    echo "Lookup took {$event->duration}ms";
});
```

## Error Handling

The library provides specific exceptions for different failure scenarios:

```php
try {
    $location = $geoip->locate($ip);
} catch (InvalidIpAddressException $e) {
    // Invalid IP format or private IP
} catch (LocationNotFoundException $e) {
    // IP not found in service database
} catch (ServiceFailedException $e) {
    // Service unavailable or API error
}
```

## Service Implementation

To create a service provider, implement the `Service` interface:

```php
use GeoIp\Contracts\Service;
use GeoIp\Location;

class MyGeoIpService implements Service
{
    public function locate(string $ip): Location
    {
        // Your implementation
        return new Location(
            ip: $ip,
            countryCode: 'US',
            countryName: 'United States'
            // ... other fields
        );
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see the [Release Notes](../../releases) for more information on what has changed recently.

## Credits

- [Ryan Colson](https://github.com/ryancco)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
