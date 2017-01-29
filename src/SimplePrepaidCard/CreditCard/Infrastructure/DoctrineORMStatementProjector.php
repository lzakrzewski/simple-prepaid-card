<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Doctrine\ORM\EntityManager;
use SimplePrepaidCard\CreditCard\Application\Query\StatementProjector;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;
use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;

class DoctrineORMStatementProjector implements StatementProjector
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function applyThatFundsWereLoaded(FundsWereLoaded $event)
    {
        $this->entityManager->persist(
            new StatementView(
                null,
                $event->creditCardId(),
                $event->holderId(),
                $event->at(),
                $event->reason(),
                $event->amount(),
                $event->availableBalance(),
                $event->balance()
            )
        );
        $this->entityManager->flush();
    }

    public function applyThatFundsWereCharged(FundsWereCharged $event)
    {
        $this->entityManager->persist(
            new StatementView(
                null,
                $event->creditCardId(),
                $event->holderId(),
                $event->at(),
                'Funds were charged',
                $event->amount(),
                $event->availableBalance(),
                $event->balance()
            )
        );
        $this->entityManager->flush();
    }
}
