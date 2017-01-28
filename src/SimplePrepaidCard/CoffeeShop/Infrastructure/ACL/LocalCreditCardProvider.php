<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Infrastructure\ACL;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Bus\MessageBus;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use SimplePrepaidCard\CreditCard\Application\Command\BlockFunds;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardIdOfHolderQuery;

//Todo: implement unimplemented
class LocalCreditCardProvider implements CreditCardProvider
{
    /** @var MessageBus */
    private $commandBus;

    /** @var CreditCardIdOfHolderQuery */
    private $creditCardId;

    public function __construct(MessageBus $commandBus, CreditCardIdOfHolderQuery $creditCardId)
    {
        $this->commandBus   = $commandBus;
        $this->creditCardId = $creditCardId;
    }

    /** {@inheritdoc} */
    public function authorizationRequest(UuidInterface $customerId, Product $product)
    {
        $creditCardId = $this->creditCardId->get($customerId);

        try {
            $this->commandBus->handle(new BlockFunds($creditCardId, (int) $product->price()->getAmount()));
        } catch (\Exception $e) {
            throw AuthorizationRequestWasDeclined::with($customerId, $product);
        }
    }

    /** {@inheritdoc} */
    public function capture(Money $amount, UuidInterface $customerId)
    {
    }

    /** {@inheritdoc} */
    public function reverse(Money $amount, UuidInterface $customerId)
    {
    }

    /** {@inheritdoc} */
    public function refund(Money $amount, UuidInterface $customerId)
    {
    }
}
