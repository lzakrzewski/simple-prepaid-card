<?php

declare(strict_types=1);

namespace tests\testServices;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\CaptureWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\RefundWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\ReverseWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\Product;

class TestCreditCardProvider implements CreditCardProvider
{
    /** @var bool */
    private static $willDecline;

    /** @var bool */
    private static $willApprove;

    /** @var CreditCardProvider */
    private $creditCardProvider;

    public function __construct(CreditCardProvider $creditCardProvider)
    {
        $this->creditCardProvider = $creditCardProvider;
    }

    /** {@inheritdoc} */
    public function authorizationRequest(UuidInterface $customerId, Product $product)
    {
        $this->handle(
            AuthorizationRequestWasDeclined::with($customerId, $product),
            function (CreditCardProvider $creditCardProvider) use ($customerId, $product) {
                $creditCardProvider->authorizationRequest($customerId, $product);
            }
        );
    }

    /** {@inheritdoc} */
    public function capture(Money $amount, UuidInterface $customerId)
    {
        $this->handle(
            CaptureWasDeclined::with($customerId),
            function (CreditCardProvider $creditCardProvider) use ($amount, $customerId) {
                $creditCardProvider->capture($amount, $customerId);
            }
        );
    }

    /** {@inheritdoc} */
    public function reverse(Money $amount, UuidInterface $customerId)
    {
        $this->handle(
            ReverseWasDeclined::with($customerId),
            function (CreditCardProvider $creditCardProvider) use ($amount, $customerId) {
                $creditCardProvider->reverse($amount, $customerId);
            }
        );
    }

    /** {@inheritdoc} */
    public function refund(Money $amount, UuidInterface $customerId)
    {
        $this->handle(
            RefundWasDeclined::with($customerId),
            function (CreditCardProvider $creditCardProvider) use ($amount, $customerId) {
                $creditCardProvider->refund($amount, $customerId);
            }
        );
    }

    public function willApprove()
    {
        self::$willApprove = true;
    }

    public function willDecline()
    {
        self::$willDecline = true;
    }

    public function reset()
    {
        self::$willApprove = null;
        self::$willDecline = null;
    }

    private function handle(\Exception $exception, callable $function)
    {
        if (self::$willApprove) {
            return;
        }

        if (self::$willDecline) {
            throw $exception;
        }

        $function($this->creditCardProvider);
    }
}
