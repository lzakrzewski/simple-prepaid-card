<?php

declare(strict_types=1);

namespace tests\testServices;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

class ContainsRecordedEventsMiddleware implements MessageBusMiddleware
{
    /** @var array */
    private $recordedEvents = [];

    public function handle($event, callable $next)
    {
        $this->recordedEvents[] = $event;

        $next($event);
    }

    public function clear()
    {
        $this->recordedEvents = [];
    }

    public function recordedEvents(): array
    {
        return $this->recordedEvents;
    }
}
