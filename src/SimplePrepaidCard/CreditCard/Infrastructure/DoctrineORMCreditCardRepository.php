<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Model\CreditCard;
use SimplePrepaidCard\CreditCard\Model\CreditCardDoesNotExist;
use SimplePrepaidCard\CreditCard\Model\CreditCardRepository;

class DoctrineORMCreditCardRepository extends EntityRepository implements CreditCardRepository
{
    /** {@inheritdoc} */
    public function add(CreditCard $creditCard)
    {
        $this->_em->persist($creditCard);
    }

    /** {@inheritdoc} */
    public function get(UuidInterface $creditCardId): CreditCard
    {
        if (null === $creditCard = $this->findOneBy(['creditCardId' => $creditCardId->toString()])) {
            throw new CreditCardDoesNotExist(sprintf('Credit card with id %s does not exist.', $creditCardId));
        }

        return $creditCard;
    }
}
