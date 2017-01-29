<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Common\Model;

interface DomainEvent
{
    public function __toString(): string;
}
