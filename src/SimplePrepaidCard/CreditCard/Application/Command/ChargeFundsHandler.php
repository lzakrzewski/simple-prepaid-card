<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Command;

use SimplePrepaidCard\CreditCard\Model\CreditCardRepository;

final class ChargeFundsHandler
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
        $creditCard->charge(Money::GBP($command->amount));
    }
}
