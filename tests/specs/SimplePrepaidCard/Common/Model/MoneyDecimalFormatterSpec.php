<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\Common\Model;

use Money\Money;
use PhpSpec\ObjectBehavior;
use SimplePrepaidCard\Common\Model\MoneyDecimalFormatter;

/** @mixin MoneyDecimalFormatter */
class MoneyDecimalFormatterSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedThrough('create');
    }

    public function it_can_format_money_to_decimal()
    {
        $this->format(Money::GBP(1234))->shouldBeLike('12.34');
    }

    public function it_can_format_negative_money_to_decimal()
    {
        $this->format(Money::GBP(-1234))->shouldBeLike('-12.34');
    }
}
