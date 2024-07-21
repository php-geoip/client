<?php

declare(strict_types=1);

namespace GeoIp\Services;

use GeoIp\Contracts\Service;
use GeoIp\Exceptions\LocationNotFoundException;
use GeoIp\Location;
use GeoIp\Services\Concerns\InteractsWithRequest;

/*
 * A runtime-only Locator service leveraging Cloudflare's 'Add visitor location headers'
 * managed transform. This service provides a partial location only in the context of
 * a web request routed through Cloudflare.
 *
 * Possible country codes include:
 * - XX: Used for clients without country code data
 * - T1: Used for clients using the Tor network
 *
 * To learn more including how to enable these headers, read here:
 * https://support.cloudflare.com/hc/en-us/articles/200168236-Configuring-IP-geolocation
 */
final class Cloudflare implements Service
{
    use InteractsWithRequest;

    public function locate(string $ip): Location
    {
        // If Cloudflare can't detect the visitor's country or detects that
        // they're connecting through TOR then throw an exception which
        // will fall back to the default location if one has been specified.
        if (in_array($this->getHeader('CF_IPCOUNTRY'), ['XX', 'T1'])) {
            throw new LocationNotFoundException($ip);
        }

        return new Location(
            ip: $ip,
            countryCode: (string) $this->getHeader('CF_IPCOUNTRY') ?: null,
            city: $this->getHeader('CF_IPCITY') ?: null,
            latitude: ($latitude = $this->getHeader('CF_IPLATITUDE')) ? (float) $latitude : null,
            longitude: ($longitude = $this->getHeader('CF_IPLONGITUDE')) ? (float) $longitude : null,
        );
    }
}
