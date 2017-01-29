<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\Bundle\AppBundle\Form\DataTransformer;

use Money\Money;
use PhpSpec\ObjectBehavior;
use SimplePrepaidCard\Bundle\AppBundle\Form\DataTransformer\MoneyToStringTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;

/** @mixin MoneyToStringTransformer */
class MoneyToStringTransformerSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf(MoneyToStringTransformer::class);
    }

    public function it_transforms_money_to_string()
    {
        $money = Money::GBP(1001);

        $this->transform($money)->shouldBe('10.01');
    }

    public function it_transforms_null_to_string()
    {
        $this->transform(null)->shouldBe('');
    }

    public function it_can_not_transform_non_money()
    {
        $this->shouldThrow(TransformationFailedException::class)->duringTransform(new \stdClass());
    }

    public function it_reverse_transforms_string_to_money()
    {
        $this->reverseTransform('11.111212')->shouldBeLike(Money::GBP(1111));
    }

    public function it_can_not_reserve_transforms_money_string()
    {
        $this->shouldThrow(TransformationFailedException::class)->duringReverseTransform('invalid');
    }
}
