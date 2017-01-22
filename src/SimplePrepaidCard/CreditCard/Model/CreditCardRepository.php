<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

interface CreditCardRepository
{
    /**
     * @param CreditCard $creditCard
     */
    public function add(CreditCard $creditCard);

    /**
     * @param UuidInterface $creditCardId
     *
     * @throws
     *
     * @return CreditCard
     */
    public function get(UuidInterface $creditCardId): CreditCard;
}
