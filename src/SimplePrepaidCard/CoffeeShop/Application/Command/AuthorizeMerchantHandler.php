<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Command;

use Money\Money;
use SimplePrepaidCard\CoffeeShop\Model\MerchantRepository;

final class AuthorizeMerchantHandler
{
    /** @var MerchantRepository */
    private $merchants;

    public function __construct(MerchantRepository $merchants)
    {
        $this->merchants = $merchants;
    }

    public function handle(AuthorizeMerchant $command)
    {
        $merchant = $this->merchants->get($command->merchantId);
        $merchant->authorize(Money::GBP($command->amount), $command->customerId);
    }
}
