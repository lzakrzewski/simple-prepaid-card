<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Twig;

use Money\Money;
use SimplePrepaidCard\Common\Model\MoneyDecimalFormatter;

class MoneyFilter extends \Twig_Extension
{
    /** @var MoneyDecimalFormatter */
    private $formatter;

    public function __construct(MoneyDecimalFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('moneyRaw', [$this, 'moneyRaw']),
            new \Twig_SimpleFilter('moneyFormatted', [$this, 'moneyFormatted']),
        ];
    }

    public function moneyRaw(Money $money): string
    {
        return $this->formatter->format($money);
    }

    public function moneyFormatted(Money $money): string
    {
        if ('GBP' == $money->getCurrency()) {
            return sprintf('%s %s', '£', $this->moneyRaw($money));
        }

        return $this->moneyRaw($money);
    }

    public function getName(): string
    {
        return 'money_filter';
    }
}
