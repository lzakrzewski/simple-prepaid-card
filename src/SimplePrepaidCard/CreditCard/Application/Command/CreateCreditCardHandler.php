<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Command;

use SimplePrepaidCard\CreditCard\Model\CreditCard;
use SimplePrepaidCard\CreditCard\Model\CreditCardRepository;

final class CreateCreditCardHandler
{
    /** @var CreditCardRepository */
    private $creditCards;

    public function __construct(CreditCardRepository $creditCards)
    {
        $this->creditCards = $creditCards;
    }

    public function handle(CreateCreditCard $command)
    {
        $this->creditCards->add(
            CreditCard::create(
                $command->creditCardId,
                $command->holderId,
                $command->holderName
            )
        );
    }
}
