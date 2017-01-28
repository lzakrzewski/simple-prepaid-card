<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Model\CreditCard;
use SimplePrepaidCard\CreditCard\Model\CreditCardAlreadyExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardDoesNotExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardRepository;

class DoctrineORMCreditCardRepository extends EntityRepository implements CreditCardRepository
{
    /** {@inheritdoc} */
    public function add(CreditCard $creditCard)
    {
        if (null !== $this->creditCard($creditCard->creditCardId())) {
            throw CreditCardAlreadyExist::with($creditCard->creditCardId());
        }

        $this->_em->persist($creditCard);
    }

    /** {@inheritdoc} */
    public function get(UuidInterface $creditCardId): CreditCard
    {
        if (null === $creditCard = $this->creditCard($creditCardId)) {
            throw CreditCardDoesNotExist::with($creditCardId);
        }

        return $creditCard;
    }

    private function creditCard(UuidInterface $creditCardId)
    {
        return $this->findOneBy(['creditCardId' => $creditCardId->toString()]);
    }
}
