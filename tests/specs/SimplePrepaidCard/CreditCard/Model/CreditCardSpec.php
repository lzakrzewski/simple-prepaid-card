<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CreditCard\Model;

use Money\Money;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Model\CannotBlockMoreThanAvailableFunds;
use SimplePrepaidCard\CreditCard\Model\CannotChargeMoreFundsThanBlocked;
use SimplePrepaidCard\CreditCard\Model\CannotUseNegativeFunds;
use SimplePrepaidCard\CreditCard\Model\CreditCard;
use tests\builders\CreditCard\CreditCardDataBuilder;

/** @mixin CreditCard */
class CreditCardSpec extends ObjectBehavior
{
    public function it_can_be_created()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->shouldBeAnInstanceOf(CreditCard::class);
    }

    public function it_can_has_credit_card_data()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), $creditCardData = CreditCardDataBuilder::create()->build()]);

        $this->creditCardData()->shouldBeLike($creditCardData);
    }

    public function it_has_0_balance_just_after_creation()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->balance()->shouldBeLike(Money::GBP(0));
    }

    public function it_has_0_available_balance_just_after_creation()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->availableBalance()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_load_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->loadFunds(Money::GBP(55), 'deposit');

        $this->availableBalance()->shouldBeLike(Money::GBP(55));
        $this->balance()->shouldBeLike(Money::GBP(55));
    }

    public function it_can_load_funds_multiple_times()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->loadFunds(Money::GBP(55), 'deposit 1');
        $this->loadFunds(Money::GBP(123), 'deposit 2');

        $this->availableBalance()->shouldBeLike(Money::GBP(178));
        $this->balance()->shouldBeLike(Money::GBP(178));
    }

    public function it_can_not_load_negative_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->shouldThrow(CannotUseNegativeFunds::class)->duringLoadFunds(Money::GBP(-55), 'deposit');
    }

    public function it_can_not_load_funds_with_invalid_currency()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringLoadFunds(Money::USD(55), 'deposit');
    }

    public function it_can_block_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');

        $this->blockFunds(Money::GBP(100));

        $this->availableBalance()->shouldBeLike(Money::GBP(0));
        $this->balance()->shouldBeLike(Money::GBP(100));
    }

    public function it_can_block_funds_multiple_times()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(178), 'deposit');

        $this->blockFunds(Money::GBP(31));
        $this->blockFunds(Money::GBP(11));

        $this->availableBalance()->shouldBeLike(Money::GBP(136));
        $this->balance()->shouldBeLike(Money::GBP(178));
    }

    public function it_can_not_block_more_than_available_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');

        $this->shouldThrow(CannotBlockMoreThanAvailableFunds::class)->duringBlockFunds(Money::GBP(101));
    }

    public function it_can_not_block_negative_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->shouldThrow(CannotUseNegativeFunds::class)->duringBlockFunds(Money::GBP(-55));
    }

    public function it_can_not_block_funds_with_invalid_currency()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringBlockFunds(Money::USD(55));
    }

    public function it_can_unblock_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(77));

        $this->unblockFunds(Money::GBP(77));

        $this->availableBalance()->shouldBeLike(Money::GBP(100));
        $this->balance()->shouldBeLike(Money::GBP(100));
    }

    public function it_can_unblock_partial_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(100));

        $this->unblockFunds(Money::GBP(50));

        $this->availableBalance()->shouldBeLike(Money::GBP(50));
        $this->balance()->shouldBeLike(Money::GBP(100));
    }

    public function it_unblock_too_much_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(77));

        $this->unblockFunds(Money::GBP(1111));

        $this->availableBalance()->shouldBeLike(Money::GBP(100));
        $this->balance()->shouldBeLike(Money::GBP(100));
    }

    public function it_can_unblock_funds_when_funds_are_not_blocked()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');

        $this->unblockFunds(Money::GBP(1111));

        $this->availableBalance()->shouldBeLike(Money::GBP(100));
        $this->balance()->shouldBeLike(Money::GBP(100));
    }

    public function it_can_not_unblock_with_negative_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(77));

        $this->shouldThrow(CannotUseNegativeFunds::class)->duringUnblockFunds(Money::GBP(-1));
    }

    public function it_can_not_unblock_with_funds_in_different_currency()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(77));

        $this->shouldThrow(\InvalidArgumentException::class)->duringUnblockFunds(Money::USD(1));
    }

    public function it_can_charge_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(100));

        $this->chargeFunds(Money::GBP(100), 'charge');

        $this->availableBalance()->shouldBeLike(Money::GBP(0));
        $this->balance()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_charge_funds_multiple_times()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(90));

        $this->chargeFunds(Money::GBP(10), 'charge');
        $this->chargeFunds(Money::GBP(40), 'charge');

        $this->availableBalance()->shouldBeLike(Money::GBP(10));
        $this->balance()->shouldBeLike(Money::GBP(50));
    }

    public function it_can_not_charge_negative_funds()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(100));

        $this->shouldThrow(CannotUseNegativeFunds::class)->duringChargeFunds(Money::GBP(-55), 'charge');
    }

    public function it_can_charge_not_funds_with_different_currency()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(100));

        $this->shouldThrow(\InvalidArgumentException::class)->duringChargeFunds(Money::USD(55), 'charge');
    }

    public function it_can_not_charge_more_funds_than_blocked()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');
        $this->blockFunds(Money::GBP(99));

        $this->shouldThrow(CannotChargeMoreFundsThanBlocked::class)->duringChargeFunds(Money::GBP(100), 'charge');
    }

    public function it_can_not_charge_where_funds_were_not_blocked()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4(), Uuid::uuid4(), CreditCardDataBuilder::create()->build()]);
        $this->loadFunds(Money::GBP(100), 'deposit');

        $this->shouldThrow(CannotChargeMoreFundsThanBlocked::class)->duringChargeFunds(Money::GBP(100), 'charge');
    }
}
