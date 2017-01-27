<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\CannotCaptureMoreThanAuthorized;
use SimplePrepaidCard\CoffeeShop\Model\CannotRefundMoreThanCaptured;
use SimplePrepaidCard\CoffeeShop\Model\CannotReverseMoreThanAuthorized;
use SimplePrepaidCard\CoffeeShop\Model\CannotUseNegativeAmount;
use SimplePrepaidCard\CoffeeShop\Model\CaptureWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CoffeeShop\Model\RefundWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\ReverseWasDeclined;

//Todo: Handle cases when refund/reverse/capture not authorized (probably on credit card provider side)
/** @mixin Merchant */
class MerchantSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedThrough('create');
    }

    public function it_can_be_created()
    {
        $this->shouldBeAnInstanceOf(Merchant::class);
    }

    public function it_has_merchant_id()
    {
        $this->merchantId()->shouldBeLike(Uuid::fromString(Merchant::MERCHANT_ID));
    }

    public function it_can_be_authorized_to_amount_by_customer()
    {
        $customerId = Uuid::uuid4();

        $this->authorize(Money::GBP(100), $customerId);

        $this->authorized()->shouldBeLike(Money::GBP(100));
        $this->authorizedBy()->shouldBeLike($customerId);
    }

    public function it_can_be_authorized_multiple_times()
    {
        $customerId = Uuid::uuid4();

        $this->authorize(Money::GBP(100), $customerId);
        $this->authorize(Money::GBP(100), $customerId);
        $this->authorize(Money::GBP(100), $customerId);

        $this->authorized()->shouldBeLike(Money::GBP(300));
        $this->authorizedBy()->shouldBeLike($customerId);
    }

    public function it_can_not_be_authorized_with_negative_amount()
    {
        $this->shouldThrow(CannotUseNegativeAmount::class)->duringAuthorize(Money::GBP(-300), Uuid::uuid4());
    }

    public function it_can_not_be_authorized_with_amount_in_different_currency()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringAuthorize(Money::USD(300), Uuid::uuid4());
    }

    public function it_can_capture_authorized(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();

        $this->authorize(Money::GBP(100), $customerId);
        $creditCardProvider->capture(Money::GBP(100), $customerId)->shouldBeCalled();

        $this->capture(Money::GBP(100), $creditCardProvider);

        $this->captured()->shouldBeLike(Money::GBP(100));
        $this->authorized()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_capture_authorized_a_few_times(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();

        $this->authorize(Money::GBP(100), $customerId);
        $creditCardProvider->capture(Money::GBP(50), $customerId)->shouldBeCalled();

        $this->capture(Money::GBP(50), $creditCardProvider);
        $this->capture(Money::GBP(50), $creditCardProvider);

        $this->captured()->shouldBeLike(Money::GBP(100));
        $this->authorized()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_not_capture_more_than_authorized(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();

        $this->authorize(Money::GBP(100), $customerId);
        $creditCardProvider->capture(Money::GBP(101), $customerId)->shouldNotBeCalled();

        $this->shouldThrow(CannotCaptureMoreThanAuthorized::class)->duringCapture(Money::GBP(101), $creditCardProvider);
    }

    public function it_can_not_capture_when_non_authorized(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();

        $creditCardProvider->capture(Money::GBP(101), $customerId)->shouldNotBeCalled();

        $this->shouldThrow(CannotCaptureMoreThanAuthorized::class)->duringCapture(Money::GBP(101), $creditCardProvider);
    }

    public function it_can_not_capture_when_credit_card_provider_declined_capture(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->capture(Argument::cetera())->willThrow(CaptureWasDeclined::class);

        $this->shouldThrow(CaptureWasDeclined::class)->duringCapture(Money::GBP(50), $creditCardProvider);
    }

    public function it_can_not_capture_negative_amount(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->capture(Money::GBP(-50), $customerId)->shouldNotBeCalled();

        $this->shouldThrow(CannotUseNegativeAmount::class)->duringCapture(Money::GBP(-50), $creditCardProvider);
    }

    public function it_can_not_capture_with_different_currency(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->capture(Money::GBP(55), $customerId)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringCapture(Money::USD(55), $creditCardProvider);
    }

    public function it_can_reverse_authorization(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->reverse(Money::GBP(100), $customerId)->shouldBeCalled();

        $this->reverse(Money::GBP(100), $creditCardProvider);

        $this->authorized()->shouldBeLike(Money::GBP(0));
        $this->captured()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_reverse_authorization_a_few_times(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->reverse(Money::GBP(50), $customerId)->shouldBeCalled();

        $this->reverse(Money::GBP(50), $creditCardProvider);
        $this->reverse(Money::GBP(50), $creditCardProvider);

        $this->authorized()->shouldBeLike(Money::GBP(0));
        $this->captured()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_not_reverse_more_than_authorized(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->reverse(Money::GBP(101), $customerId)->shouldNotBeCalled();

        $this->shouldThrow(CannotReverseMoreThanAuthorized::class)->duringReverse(Money::GBP(101), $creditCardProvider);
    }

    public function it_can_not_reverse_when_credit_card_provider_declined_reverse(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->reverse(Argument::cetera())->willThrow(ReverseWasDeclined::class);

        $this->shouldThrow(ReverseWasDeclined::class)->duringReverse(Money::GBP(55), $creditCardProvider);
    }

    public function it_can_not_reverse_negative_amount(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->reverse(Money::GBP(-50), $customerId)->shouldNotBeCalled();

        $this->shouldThrow(CannotUseNegativeAmount::class)->duringReverse(Money::GBP(-50), $creditCardProvider);
    }

    public function it_can_not_reverse_with_different_currency(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $this->authorize(Money::GBP(100), $customerId);

        $creditCardProvider->reverse(Money::GBP(-10), $customerId)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringReverse(Money::USD(10), $creditCardProvider);
    }

    public function it_can_refund_captured(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $creditCardProvider->capture(Argument::cetera())->shouldBeCalled();
        $creditCardProvider->refund(Argument::cetera())->shouldBeCalled();
        $this->authorize(Money::GBP(100), $customerId);
        $this->capture(Money::GBP(100), $creditCardProvider);

        $this->refund(Money::GBP(100), $creditCardProvider);

        $this->captured()->shouldBeLike(Money::GBP(0));
        $this->authorized()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_refund_captured_a_few_times(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $creditCardProvider->capture(Argument::cetera())->shouldBeCalled();
        $creditCardProvider->refund(Argument::cetera())->shouldBeCalled();
        $this->authorize(Money::GBP(100), $customerId);
        $this->capture(Money::GBP(100), $creditCardProvider);

        $this->refund(Money::GBP(50), $creditCardProvider);
        $this->refund(Money::GBP(50), $creditCardProvider);

        $this->captured()->shouldBeLike(Money::GBP(0));
        $this->authorized()->shouldBeLike(Money::GBP(0));
    }

    public function it_can_not_refund_more_than_captured(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $creditCardProvider->capture(Argument::cetera())->shouldBeCalled();
        $creditCardProvider->refund(Argument::cetera())->shouldNotBeCalled();
        $this->authorize(Money::GBP(100), $customerId);
        $this->capture(Money::GBP(100), $creditCardProvider);

        $this->shouldThrow(CannotRefundMoreThanCaptured::class)->duringRefund(Money::GBP(101), $creditCardProvider);
    }

    public function it_can_not_refund_when_credit_card_provider_declined_refund(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $creditCardProvider->capture(Argument::cetera())->shouldBeCalled();
        $creditCardProvider->refund(Argument::cetera())->willThrow(RefundWasDeclined::class);
        $this->authorize(Money::GBP(100), $customerId);
        $this->capture(Money::GBP(100), $creditCardProvider);

        $this->shouldThrow(RefundWasDeclined::class)->duringRefund(Money::GBP(100), $creditCardProvider);
    }

    public function it_can_not_refund_negative_amount(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $creditCardProvider->capture(Argument::cetera())->shouldBeCalled();
        $creditCardProvider->refund(Argument::cetera())->shouldNotBeCalled();
        $this->authorize(Money::GBP(100), $customerId);
        $this->capture(Money::GBP(100), $creditCardProvider);

        $this->shouldThrow(CannotUseNegativeAmount::class)->duringRefund(Money::GBP(-100), $creditCardProvider);
    }

    public function it_can_not_refund_with_different_currency(CreditCardProvider $creditCardProvider)
    {
        $customerId = Uuid::uuid4();
        $creditCardProvider->capture(Argument::cetera())->shouldBeCalled();
        $creditCardProvider->refund(Argument::cetera())->shouldNotBeCalled();
        $this->authorize(Money::GBP(100), $customerId);
        $this->capture(Money::GBP(100), $creditCardProvider);

        $this->shouldThrow(\InvalidArgumentException::class)->duringRefund(Money::USD(100), $creditCardProvider);
    }
}
