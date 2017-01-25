<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

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
}
