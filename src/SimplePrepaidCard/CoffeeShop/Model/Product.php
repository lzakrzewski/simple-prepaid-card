<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use SimplePrepaidCard\Common\Model\ValueObject;

final class Product implements ValueObject
{
    const COFFEE_PRODUCT_ID    = 'coffee';
    const COFFEE_PRODUCT_NAME  = self::COFFEE_PRODUCT_ID;
    const COFFEE_PRODUCT_PRICE = 500;

    /** @var string */
    private $productId;

    /** @var string */
    private $name;

    /** @var Money */
    private $price;

    public function __construct(string $productId, string $name, Money $price)
    {
        $this->productId = $productId;
        $this->name      = $name;
        $this->price     = $price;
    }

    public static function coffee(): self
    {
        return new self(
            self::COFFEE_PRODUCT_ID,
            self::COFFEE_PRODUCT_NAME,
            Money::GBP(self::COFFEE_PRODUCT_PRICE)
        );
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): Money
    {
        return $this->price;
    }
}
