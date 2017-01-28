<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Infrastructure\ACL;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Bus\MessageBus;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CaptureWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use SimplePrepaidCard\CoffeeShop\Model\RefundWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\ReverseWasDeclined;
use SimplePrepaidCard\CreditCard\Application\Command\BlockFunds;
use SimplePrepaidCard\CreditCard\Application\Command\ChargeFunds;
use SimplePrepaidCard\CreditCard\Application\Command\LoadFunds;
use SimplePrepaidCard\CreditCard\Application\Command\UnblockFunds;
use SimplePrepaidCard\CreditCard\Application\Query\CreditCardIdOfHolderQuery;

//Todo: add reasons to errors
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
        try {
            $creditCardId = $this->creditCardId($customerId);

            $this->commandBus->handle(new BlockFunds($creditCardId, (int) $product->price()->getAmount()));
        } catch (\Exception $e) {
            throw AuthorizationRequestWasDeclined::with($customerId, $product);
        }
    }

    /** {@inheritdoc} */
    public function capture(Money $amount, UuidInterface $customerId)
    {
        try {
            $creditCardId = $this->creditCardId($customerId);

            $this->commandBus->handle(new ChargeFunds($creditCardId, (int) $amount->getAmount()));
        } catch (\Exception $e) {
            throw CaptureWasDeclined::with($customerId);
        }
    }

    /** {@inheritdoc} */
    public function reverse(Money $amount, UuidInterface $customerId)
    {
        try {
            $creditCardId = $this->creditCardId($customerId);

            $this->commandBus->handle(new UnblockFunds($creditCardId, (int) $amount->getAmount()));
        } catch (\Exception $e) {
            throw ReverseWasDeclined::with($customerId);
        }
    }

    /** {@inheritdoc} */
    public function refund(Money $amount, UuidInterface $customerId)
    {
        try {
            $creditCardId = $this->creditCardId($customerId);

            $this->commandBus->handle(new LoadFunds($creditCardId, (int) $amount->getAmount()));
        } catch (\Exception $e) {
            throw RefundWasDeclined::with($customerId);
        }
    }

    private function creditCardId(UuidInterface $customerId): UuidInterface
    {
        return $this->creditCardId->get($customerId);
    }
}
