<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

//Todo: change arguments order
interface CreditCardProvider
{
    /**
     * @param UuidInterface $customerId
     * @param Product       $product
     *
     * @throws AuthorizationRequestWasDeclined
     *
     * @return mixed
     */
    public function authorizationRequest(UuidInterface $customerId, Product $product);

    /**
     * @param Money         $amount
     * @param UuidInterface $customerId
     *
     * @throws CaptureWasDeclined
     */
    public function capture(Money $amount, UuidInterface $customerId);

    /**
     * @param Money         $amount
     * @param UuidInterface $customerId
     *
     * @throws ReverseWasDeclined
     */
    public function reverse(Money $amount, UuidInterface $customerId);

    /**
     * @param Money         $amount
     * @param UuidInterface $customerId
     *
     * @throws RefundWasDeclined
     */
    public function refund(Money $amount, UuidInterface $customerId);
}
