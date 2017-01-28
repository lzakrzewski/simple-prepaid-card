<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

interface CreditCardQuery
{
    public function get(): CreditCardView;
}
