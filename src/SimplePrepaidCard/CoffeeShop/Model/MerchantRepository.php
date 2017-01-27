<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

interface MerchantRepository
{
    /**
     * @param Merchant $merchant
     */
    public function add(Merchant $merchant);

    /**
     * @param UuidInterface $merchantId
     *
     * @throws MerchantDoesNotExist
     *
     * @return Merchant
     */
    public function get(UuidInterface $merchantId): Merchant;
}
