<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

final class CannotChargeMoreFundsThanBlocked extends \DomainException
{
    public static function with(UuidInterface $creditCardId): self
    {
        return new self(
            sprintf(
                'Can not charge more funds than were blocked from credit card with id "%s".',
                $creditCardId
            )
        );
    }
}
