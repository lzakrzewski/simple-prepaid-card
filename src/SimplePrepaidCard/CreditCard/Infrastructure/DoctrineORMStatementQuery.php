<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardIdOfHolderQuery;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardOfHolderDoesNotExist;
use SimplePrepaidCard\CreditCard\Application\Query\StatementQuery;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;

class DoctrineORMStatementQuery implements StatementQuery
{
    /** @var EntityManager */
    private $entityManager;

    /** @var CreditCardIdOfHolderQuery */
    private $creditCardId;

    public function __construct(EntityManager $entityManager, CreditCardIdOfHolderQuery $creditCardId)
    {
        $this->entityManager = $entityManager;
        $this->creditCardId  = $creditCardId;
    }

    /** {@inheritdoc} */
    public function ofHolder(UuidInterface $holderId): array
    {
        try {
            $creditCardId = $this->creditCardId->get($holderId);
        } catch (CreditCardOfHolderDoesNotExist $e) {
            return [];
        }

        return $this->entityManager
            ->createQueryBuilder()
            ->select('s')
            ->from(StatementView::class, 's')
            ->orderBy('s.date', 'DESC')
            ->where('s.holderId = :holderId')
            ->andWhere('s.creditCardId = :creditCardId')
            ->setParameter('holderId', $holderId)
            ->setParameter('creditCardId', $creditCardId)
            ->getQuery()
            ->getResult();
    }
}
