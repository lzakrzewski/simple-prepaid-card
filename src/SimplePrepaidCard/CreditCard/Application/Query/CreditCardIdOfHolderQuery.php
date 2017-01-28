<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

use Ramsey\Uuid\UuidInterface;

interface CreditCardIdOfHolderQuery
{
    /**
     * @param UuidInterface $holderId
     *
     * @throws CreditCardOfHolderDoesNotExist
     *
     * @return UuidInterface
     */
    public function get(UuidInterface $holderId): UuidInterface;
}
