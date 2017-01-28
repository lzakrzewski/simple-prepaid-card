<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\Bundle\AppBundle\Twig;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use PhpSpec\ObjectBehavior;
use SimplePrepaidCard\Bundle\AppBundle\Twig\MoneyFilter;

/** @mixin MoneyFilter */
class MoneyFilterSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(new DecimalMoneyFormatter(new ISOCurrencies()));
    }

    public function it_can_convert_money_to_raw_value()
    {
        $this->moneyRaw(Money::GBP(101))->shouldBe('1.01');
    }

    public function it_can_convert_money_to_formatted_string()
    {
        $this->moneyFormatted(Money::GBP(202))->shouldBe('£ 2.02');
    }
}
