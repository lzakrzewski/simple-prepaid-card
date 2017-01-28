<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardIdOfHolderQuery;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardOfHolderDoesNotExist;
use SimplePrepaidCard\CreditCard\Model\CreditCard;

class DoctrineORMCreditCardIdOfHolderQuery implements CreditCardIdOfHolderQuery
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** {@inheritdoc} */
    public function get(UuidInterface $holderId): UuidInterface
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('cc.creditCardId')
            ->from(CreditCard::class, 'cc')
            ->setMaxResults(1)
            ->getQuery()
            ->getScalarResult();

        if (empty($result)) {
            throw CreditCardOfHolderDoesNotExist::with($holderId);
        }

        return Uuid::fromString($result[0]['creditCardId']);
    }
}
