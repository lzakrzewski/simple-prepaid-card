<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Infrastructure;

use Doctrine\ORM\EntityManager;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Application\Query\MerchantQuery;
use SimplePrepaidCard\CoffeeShop\Application\Query\MerchantView;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;

class DoctrineORMMerchantQuery implements MerchantQuery
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(): MerchantView
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('m.merchantId, m.authorized.amount, m.authorized.currency.code, m.captured.amount, m.captured.currency.code')
            ->from(Merchant::class, 'm')
            ->setMaxResults(1)
            ->where('m.merchantId = :merchantId')
            ->setParameter('merchantId', Merchant::MERCHANT_ID)
            ->getQuery()
            ->getResult();

        if (empty($result)) {
            return new MerchantView();
        }

        return new MerchantView(
            Uuid::fromString($result[0]['merchantId']),
            new Money($result[0]['authorized.amount'], new Currency($result[0]['authorized.currency.code'])),
            new Money($result[0]['captured.amount'], new Currency($result[0]['captured.currency.code']))
        );
    }
}
