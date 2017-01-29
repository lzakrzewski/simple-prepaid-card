<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Common\Model;

use Money\Money;

final class MoneyDecimalFormatter
{
    public static function create(): self
    {
        return new self();
    }

    public function format(Money $money)
    {
        return sprintf('%.2f', (int) $money->getAmount() / 100.0);
    }
}
