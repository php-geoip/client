<?php

declare(strict_types=1);

namespace GeoIp;

use Psr\EventDispatcher\EventDispatcherInterface;

class SimpleEventDispatcher implements EventDispatcherInterface
{
    /** @var array<class-string, callable[]> */
    private array $listeners = [];

    public function listen(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch(object $event): void
    {
        $eventClass = get_class($event);

        foreach ($this->listeners[$eventClass] ?? [] as $listener) {
            $listener($event);
        }
    }
}
