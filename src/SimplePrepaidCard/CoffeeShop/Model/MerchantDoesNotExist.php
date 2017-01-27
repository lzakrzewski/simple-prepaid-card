<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

final class MerchantDoesNotExist extends \DomainException
{
    public static function with(UuidInterface $merchantId): self
    {
        return new self(sprintf('Merchant with id "%s" does not exist.', $merchantId));
    }
}
