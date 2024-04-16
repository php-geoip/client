# GeoIp

[![Latest Version on Packagist](https://img.shields.io/packagist/v/geoip/client.svg?style=flat-square)](https://packagist.org/packages/geoip/client)
[![Tests](https://github.com/geoip/client/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/geoip/client/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/geoip/client.svg?style=flat-square)](https://packagist.org/packages/geoip/client)

This package provides an opinionated framework for resolving geolocations from IP addresses.

## Installation

You can install the package via composer:

```bash
composer require php-geoip/client:^0.1
```

## Usage

```php
use GeoIp\GeoIp;

$geoip = new GeoIp($locator, $cache);

$location = $geoip->locate($ip);

$location->countryCode; // 'US'
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
