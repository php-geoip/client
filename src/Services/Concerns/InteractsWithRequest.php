<?php

declare(strict_types=1);

namespace GeoIp\Services\Concerns;

trait InteractsWithRequest
{
    protected function getHeader(string $header): ?string
    {
        if (! str_starts_with($header, 'HTTP_')) {
            $header = 'HTTP_' . $header;
        }

        return $_SERVER[strtoupper($header)] ?? null;
    }
}
