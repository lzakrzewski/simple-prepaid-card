<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

use Ramsey\Uuid\UuidInterface;

interface StatementQuery
{
    public function ofHolder(UuidInterface $creditCardId): array;
}
