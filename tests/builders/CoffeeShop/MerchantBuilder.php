<?php

declare(strict_types=1);

namespace tests\builders\CoffeeShop;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use tests\builders\Builder;

final class MerchantBuilder implements Builder
{
    /** @var UuidInterface */
    private $merchantId;

    /** @var Money */
    private $authorizedTo;

    /** @var UuidInterface */
    private $authorizedBy;

    private function __construct(UuidInterface $merchantId, UuidInterface $authorizedBy, Money $authorizedTo)
    {
        $this->merchantId   = $merchantId;
        $this->authorizedBy = $authorizedBy;
        $this->authorizedTo = $authorizedTo;
    }

    public static function create(): self
    {
        return new self(Uuid::uuid4(), Uuid::uuid4(), Money::GBP(rand(10, 1000)));
    }

    public function withMerchantId(UuidInterface $merchantId): self
    {
        $copy             = $this->copy();
        $copy->merchantId = $merchantId;

        return $copy;
    }

    public function authorizedTo(Money $amount): self
    {
        $copy               = $this->copy();
        $copy->authorizedTo = $amount;

        return $copy;
    }

    public function authorizedBy(UuidInterface $authorizedBy): self
    {
        $copy               = $this->copy();
        $copy->authorizedBy = $authorizedBy;

        return $copy;
    }

    public function build(): Merchant
    {
        $merchant = new Merchant($this->merchantId);

        if ($this->authorizedTo->isPositive()) {
            $merchant->authorize($this->authorizedTo, $this->authorizedBy);
        }

        $merchant->eraseMessages();

        return $merchant;
    }

    private function copy(): self
    {
        return new self($this->merchantId, $this->authorizedBy, $this->authorizedTo);
    }
}
