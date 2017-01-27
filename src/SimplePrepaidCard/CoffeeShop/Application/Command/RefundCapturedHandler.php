<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Command;

use Money\Money;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\MerchantRepository;

final class RefundCapturedHandler
{
    /** @var MerchantRepository */
    private $merchants;

    /** @var CreditCardProvider */
    private $creditCardProvider;

    public function __construct(MerchantRepository $merchants, CreditCardProvider $creditCardProvider)
    {
        $this->merchants          = $merchants;
        $this->creditCardProvider = $creditCardProvider;
    }

    public function handle(RefundCaptured $command)
    {
        $merchant = $this->merchants->get($command->merchantId);
        $merchant->refund(Money::GBP($command->amount), $this->creditCardProvider);
    }
}
