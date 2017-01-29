<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Command;

use Money\Money;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\MerchantRepository;

final class CaptureAuthorizationHandler
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

    public function handle(CaptureAuthorization $command)
    {
        $merchant = $this->merchants->get($command->merchantId);
        $merchant->capture(Money::GBP($command->amount), $this->creditCardProvider);
    }
}
