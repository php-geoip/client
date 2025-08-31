<?php

declare(strict_types=1);

namespace GeoIp\Services;

use GeoIp\Contracts\Service;
use GeoIp\Location;
use GeoIp\Services\Concerns\InteractsWithRequest;

/*
 * A runtime-only Locator service leveraging CloudFront's 'additional geolocation headers'
 * managed transform. This service provides a location only in the context of
 * a web request routed through CloudFront.
 *
 * To learn more including how to enable these headers, read here:
 * https://aws.amazon.com/about-aws/whats-new/2020/07/cloudfront-geolocation-headers/
 */
final class CloudFront implements Service
{
    use InteractsWithRequest;

    public function locate(string $ip): Location
    {
        return new Location(
            ip: $ip,
            countryCode: $this->getHeader('CloudFront-Viewer-Country') ?: null,
            countryName: $this->getHeader('CloudFront-Viewer-Country-Name') ?: null,
            stateCode: $this->getHeader('CloudFront-Viewer-Country-Region') ?: null,
            stateName: $this->getHeader('CloudFront-Viewer-Country-Region-Name') ?: null,
            city: $this->getHeader('CloudFront-Viewer-City') ?: null,
            postalCode: $this->getHeader('CloudFront-Viewer-Postal-Code') ?: null,
            latitude: ($latitude = $this->getHeader('CloudFront-Viewer-Latitude')) ? (float) $latitude : null,
            longitude: ($longitude = $this->getHeader('CloudFront-Viewer-Longitude')) ? (float) $longitude : null,
            timezone: $this->getHeader('CloudFront-Viewer-Time-Zone') ?: null,
        );
    }
}
