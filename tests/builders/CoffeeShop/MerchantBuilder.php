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

    /** @var UuidInterface */
    private $authorizedBy;

    /** @var Money */
    private $authorized;

    /** @var Money */
    private $captured;

    private function __construct(UuidInterface $merchantId, UuidInterface $authorizedBy, Money $authorized, Money $captured)
    {
        $this->merchantId   = $merchantId;
        $this->authorizedBy = $authorizedBy;
        $this->authorized   = $authorized;
        $this->captured     = $captured;
    }

    public static function create(): self
    {
        return new self(Uuid::uuid4(), Uuid::uuid4(), Money::GBP(rand(100, 1000)), Money::GBP(0));
    }

    public function withMerchantId(UuidInterface $merchantId): self
    {
        $copy             = $this->copy();
        $copy->merchantId = $merchantId;

        return $copy;
    }

    public function authorizedTo(Money $amount): self
    {
        $copy             = $this->copy();
        $copy->authorized = $amount;

        return $copy;
    }

    public function authorizedBy(UuidInterface $authorizedBy): self
    {
        $copy               = $this->copy();
        $copy->authorizedBy = $authorizedBy;

        return $copy;
    }

    public function withCaptured(Money $amount): self
    {
        $copy           = $this->copy();
        $copy->captured = $amount;

        return $copy;
    }

    public function build(): Merchant
    {
        $merchant = new Merchant($this->merchantId);

        if ($this->authorized->isPositive()) {
            $merchant->authorize($this->authorized, $this->authorizedBy);
        }

        if ($this->captured->isPositive()) {
            $merchant->capture($this->captured, new CreditCardProviderStub());
            $merchant->authorize($this->captured, $this->authorizedBy);
        }

        $merchant->eraseMessages();

        return $merchant;
    }

    private function copy(): self
    {
        return new self($this->merchantId, $this->authorizedBy, $this->authorized, $this->captured);
    }
}
