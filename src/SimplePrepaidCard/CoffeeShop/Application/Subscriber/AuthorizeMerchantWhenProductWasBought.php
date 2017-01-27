<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Subscriber;

use Ramsey\Uuid\Uuid;
use SimpleBus\Message\Bus\MessageBus;
use SimplePrepaidCard\CoffeeShop\Application\Command\AuthorizeMerchant;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use SimplePrepaidCard\CoffeeShop\Model\ProductWasBought;

final class AuthorizeMerchantWhenProductWasBought
{
    /** @var MessageBus */
    private $commandBus;

    public function __construct(MessageBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function authorize(ProductWasBought $event)
    {
        $this->commandBus->handle(
            new AuthorizeMerchant(
                Uuid::fromString(Merchant::MERCHANT_ID),
                (int) $event->product()->price()->getAmount()
            )
        );
    }
}
