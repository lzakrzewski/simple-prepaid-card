<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Doctrine\ORM\EntityManager;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardQuery;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardView;
use SimplePrepaidCard\CreditCard\Model\CreditCard;
use SimplePrepaidCard\CreditCard\Model\Holder;

class DoctrineORMCreditCardQuery implements CreditCardQuery
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(): CreditCardView
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('cc.creditCardId, cc.balance.amount, cc.balance.currency.code, cc.availableBalance.amount, cc.availableBalance.currency.code')
            ->from(CreditCard::class, 'cc')
            ->setMaxResults(1)
            ->where('cc.holderId = :holderId')
            ->addOrderBy('cc.id', 'DESC')
            ->getQuery()
            ->setParameter('holderId', Holder::HOLDER_ID)
            ->getResult();

        if (empty($result)) {
            return new CreditCardView();
        }

        return new CreditCardView(
            Uuid::fromString($result[0]['creditCardId']),
            new Money($result[0]['balance.amount'], new Currency($result[0]['balance.currency.code'])),
            new Money($result[0]['availableBalance.amount'], new Currency($result[0]['availableBalance.currency.code']))
        );
    }
}
