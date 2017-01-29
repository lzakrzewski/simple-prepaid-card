<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Command;

use Money\Money;
use SimplePrepaidCard\CreditCard\Model\CreditCardRepository;

class LoadFundsHandler
{
    /** @var CreditCardRepository */
    private $creditCards;

    public function __construct(CreditCardRepository $creditCards)
    {
        $this->creditCards = $creditCards;
    }

    public function handle(LoadFunds $command)
    {
        $creditCard = $this->creditCards->get($command->creditCardId);
        $creditCard->loadFunds(Money::GBP($command->amount), $command->reason);
    }
}
