<?php

declare(strict_types=1);

namespace tests\testServices;

use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Product;

class TestCreditCardProvider implements CreditCardProvider
{
    /** @var bool */
    private static $willDecline;

    /** @var bool */
    private static $willApprove;

    /** {@inheritdoc} */
    public function authorizationRequest(UuidInterface $customerId, Product $product)
    {
        if (self::$willApprove) {
            return;
        }

        if (self::$willDecline) {
            throw AuthorizationRequestWasDeclined::with($customerId, $product);
        }
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
}
