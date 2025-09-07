<?php

declare(strict_types=1);

namespace GeoIp\Tests\Mocks;

use Psr\EventDispatcher\EventDispatcherInterface;

class MockEventDispatcher implements EventDispatcherInterface
{
    /** @var array<class-string, callable[]> */
    private array $listeners = [];

    /**
     * @param class-string $class
     */
    public function listen(string $class, callable $listener): void
    {
        $this->listeners[$class][] = $listener;
    }

    public function dispatch(object $event): object
    {
        $class = get_class($event);

        foreach ($this->listeners[$class] ?? [] as $listener) {
            $listener($event);
        }

        return $event;
    }
}
