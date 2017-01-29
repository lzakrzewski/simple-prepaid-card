<?php

declare(strict_types=1);

namespace tests\builders\CreditCard;

use SimplePrepaidCard\CreditCard\Model\CreditCardData;
use tests\builders\Builder;

class CreditCardDataBuilder implements Builder
{
    public static function create(): self
    {
        return new self();
    }

    public function build(): CreditCardData
    {
        $now = new \DateTime();

        return CreditCardData::fromRawValues(
            'John Doe',
            '6011111111111117',
            123,
            (int) $now->format('y') + 1,
            (int) $now->format('m')
        );
    }
}
