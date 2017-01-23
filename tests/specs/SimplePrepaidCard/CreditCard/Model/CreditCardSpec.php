<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CreditCard\Model;

use Money\Money;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Model\CannotBlockMoreThanAvailableFunds;
use SimplePrepaidCard\CreditCard\Model\CannotLoadNegativeFunds;
use SimplePrepaidCard\CreditCard\Model\CreditCard;

/** @mixin CreditCard */
class CreditCardSpec extends ObjectBehavior
{
    public function it_can_be_created()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->shouldBeAnInstanceOf(CreditCard::class);
    }

    public function it_has_0_balance_just_after_creation()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->balance()->shouldBeLike(Money::GBP(0));
    }

    public function it_has_0_available_balance_just_after_creation()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->availableBalance()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_load_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->loadFunds(Money::GBP(55));

        $this->availableBalance()->shouldBeLike(Money::GBP(55));
        $this->balance()->shouldBeLike(Money::GBP(55));
    }

    public function it_can_load_funds_multiple_times()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->loadFunds(Money::GBP(55));
        $this->loadFunds(Money::GBP(123));

        $this->availableBalance()->shouldBeLike(Money::GBP(178));
        $this->balance()->shouldBeLike(Money::GBP(178));
    }

    public function it_can_not_load_negative_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->shouldThrow(CannotLoadNegativeFunds::class)->duringLoadFunds(Money::GBP(-55));
    }

    public function it_can_not_load_funds_with_invalid_currency()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->shouldThrow(\InvalidArgumentException::class)->duringLoadFunds(Money::USD(55));
    }

    public function it_can_block_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);
        $this->loadFunds(Money::GBP(100));

        $this->blockFunds(Money::GBP(100));

        $this->availableBalance()->shouldBeLike(Money::GBP(0));
        $this->balance()->shouldBeLike(Money::GBP(100));
    }

    public function it_can_block_funds_multiple_times()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);
        $this->loadFunds(Money::GBP(178));

        $this->blockFunds(Money::GBP(31));
        $this->blockFunds(Money::GBP(11));

        $this->availableBalance()->shouldBeLike(Money::GBP(136));
        $this->balance()->shouldBeLike(Money::GBP(178));
    }

    public function it_can_not_block_more_than_available_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);
        $this->loadFunds(Money::GBP(100));

        $this->shouldThrow(CannotBlockMoreThanAvailableFunds::class)->duringBlockFunds(Money::GBP(101));
    }

    public function it_can_not_block_negative_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->shouldThrow(CannotLoadNegativeFunds::class)->duringBlockFunds(Money::GBP(-55));
    }

    public function it_can_not_block_funds_with_invalid_currency()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), 'John Doe']);

        $this->shouldThrow(\InvalidArgumentException::class)->duringBlockFunds(Money::USD(55));
    }
}
