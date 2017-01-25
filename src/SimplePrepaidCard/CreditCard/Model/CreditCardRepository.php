<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

interface CreditCardRepository
{
    /**
     * @param CreditCard $creditCard
     *
     * @throws CreditCardAlreadyExist
     */
    public function add(CreditCard $creditCard);

    /**
     * @param UuidInterface $creditCardId
     *
     * @throws CreditCardDoesNotExist
     *
     * @return CreditCard
     */
    public function get(UuidInterface $creditCardId): CreditCard;

    /**
     * @param UuidInterface $holderId
     *
     * @throws CreditCardOfCardHolderDoesNotExist
     *
     * @return UuidInterface
     */
    public function creditCardIdOfHolder(UuidInterface $holderId): UuidInterface;
}
