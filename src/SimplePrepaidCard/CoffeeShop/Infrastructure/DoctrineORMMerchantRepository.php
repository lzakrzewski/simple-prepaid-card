<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Infrastructure;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CoffeeShop\Model\MerchantDoesNotExist;
use SimplePrepaidCard\CoffeeShop\Model\MerchantRepository;

class DoctrineORMMerchantRepository extends EntityRepository implements MerchantRepository
{
    /** {@inheritdoc} */
    public function add(Merchant $merchant)
    {
        $this->_em->persist($merchant);
    }

    /** {@inheritdoc} */
    public function get(UuidInterface $merchantId): Merchant
    {
        if (null === $merchant = $this->findOneBy(['merchantId' => $merchantId])) {
            throw MerchantDoesNotExist::with($merchantId);
        }

        return $merchant;
    }
}
