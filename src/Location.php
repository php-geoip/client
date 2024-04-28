<?php

declare(strict_types=1);

namespace GeoIp;

readonly class Location
{
    public function __construct(
        public string      $ip,
        public string|null $countryCode = null,
        public string|null $countryName = null,
        public string|null $stateCode = null,
        public string|null $stateName = null,
        public string|null $city = null,
        public string|null $postalCode = null,
        public string|null $continent = null,
        public float|null  $latitude = null,
        public float|null  $longitude = null,
        public string|null $timezone = null,
        public string|null $currency = null,
        public bool        $isDefault = false,
    ) {
    }

    public function clone(...$properties): Location
    {
        $existing = get_object_vars($this);

        return new static(
            ...array_replace($existing, array_intersect_key($properties, $existing))
        );
    }
}
