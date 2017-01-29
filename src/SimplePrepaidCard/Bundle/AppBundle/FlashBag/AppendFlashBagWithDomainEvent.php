<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\FlashBag;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimplePrepaidCard\Common\Model\DomainEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class AppendFlashBagWithDomainEvent implements MessageBusMiddleware
{
    /** @var FlashBag */
    private $flashBag;

    public function __construct(FlashBag $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function handle($event, callable $next)
    {
        if ($event instanceof DomainEvent) {
            $this->flashBag->add('success', $event->__toString());
        }

        $next($event);
    }
}
