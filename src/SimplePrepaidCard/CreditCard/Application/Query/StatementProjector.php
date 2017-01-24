<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;

interface StatementProjector
{
    public function applyThatFundsWereLoaded(FundsWereLoaded $event);

    public function applyThatFundsWereCharged(FundsWereCharged $event);
}
