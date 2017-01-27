<?php

declare(strict_types=1);

namespace tests\testServices;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CaptureWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use SimplePrepaidCard\CoffeeShop\Model\RefundWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\ReverseWasDeclined;

class TestCreditCardProvider implements CreditCardProvider
{
    /** @var bool */
    private static $willDecline;

    /** @var bool */
    private static $willApprove;

    /** {@inheritdoc} */
    public function authorizationRequest(UuidInterface $customerId, Product $product)
    {
        $this->handle(AuthorizationRequestWasDeclined::with($customerId, $product));
    }

    /** {@inheritdoc} */
    public function capture(Money $amount, UuidInterface $customerId)
    {
        $this->handle(CaptureWasDeclined::with($customerId));
    }

    /** {@inheritdoc} */
    public function reverse(Money $amount, UuidInterface $customerId)
    {
        $this->handle(ReverseWasDeclined::with($customerId));
    }

    /** {@inheritdoc} */
    public function refund(Money $amount, UuidInterface $customerId)
    {
        $this->handle(RefundWasDeclined::with($customerId));
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

    private function handle(\Exception $exception)
    {
        if (self::$willApprove) {
            return;
        }

        if (self::$willDecline) {
            throw $exception;
        }
    }
}
