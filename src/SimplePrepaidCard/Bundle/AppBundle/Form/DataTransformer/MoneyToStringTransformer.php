<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Form\DataTransformer;

use Money\Currency;
use Money\Money;
use SimplePrepaidCard\Common\Model\MoneyDecimalFormatter;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MoneyToStringTransformer implements DataTransformerInterface
{
    /** {@inheritdoc} */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof Money) {
            throw new TransformationFailedException('Expected a \Money\Money');
        }

        return MoneyDecimalFormatter::create()->format($value);
    }

    /** {@inheritdoc} */
    public function reverseTransform($value): Money
    {
        if (!is_numeric($value)) {
            throw new TransformationFailedException(sprintf('Expected numeric value but "%s" given', $value));
        }

        try {
            $money = new Money((int) ((float) $value * 100), new Currency('GBP'));
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage());
        }

        return $money;
    }
}
