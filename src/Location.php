<?php

declare(strict_types=1);

namespace GeoIp;

final readonly class Location
{
    public function __construct(
        public string $ip,
        public ?string $countryCode = null,
        public ?string $countryName = null,
        public ?string $stateCode = null,
        public ?string $stateName = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $continent = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $timezone = null,
        public ?string $currency = null,
        public bool $isDefault = false,
    ) {
    }

    public function clone(...$properties): Location
    {
        $existing = get_object_vars($this);

        return new Location(
            ...array_replace($existing, array_intersect_key($properties, $existing))
        );
    }
}
