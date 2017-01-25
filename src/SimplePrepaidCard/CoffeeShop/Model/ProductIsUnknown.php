<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

final class ProductIsUnknown extends \DomainException
{
    public static function with(string $productId): self
    {
        return new self(sprintf('Product with id "%s" is unknown.', $productId));
    }
}
