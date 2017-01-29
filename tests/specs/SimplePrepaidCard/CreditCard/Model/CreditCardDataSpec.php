<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CreditCard\Model;

use PhpSpec\ObjectBehavior;
use SimplePrepaidCard\CreditCard\Model\CreditCardData;

/** @mixin CreditCardData */
class CreditCardDataSpec extends ObjectBehavior
{
    public function it_can_create_credit_card_data_from_raw_values()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '6011111111111117',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->shouldBeAnInstanceOf(CreditCardData::class);
    }

    public function it_stores_expiration_date()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '601111 111111 1117',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->expirationDate()
            ->format('Y-m')
            ->shouldBe(
                sprintf('%d-%02d', $now->format('Y') + 1, $now->format('m'))
            );
    }

    public function it_stores_hashed_credit_card_number()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '6011111111111117',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->number()->shouldNotBe('6011111111111117');
    }

    public function it_stores_hashed_cvv()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '6011111111111117',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->cvv()->shouldNotBe('123');
    }

    public function it_stores_holder()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '6011111111111117',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->holder()->shouldBe('John Doe');
    }

    public function it_can_not_create_credit_card_data_with_invalid_expiry_date()
    {
        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '6011111111111117',
                123,
                99,
                99,
            ]
        );

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_can_not_create_credit_card_data_with_expiry_date_from_past()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '6011111111111117',
                123,
                (int) $now->format('y') - 1,
                (int) $now->format('m'),
            ]
        );

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_can_not_create_credit_card_data_with_invalid_credit_card_number()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                'abcd',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_can_not_create_credit_card_data_with_too_long_credit_card_number()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '11111111111111111111111111111111111',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_can_not_create_credit_card_data_with_too_short_credit_card_number()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '123',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('mm'),
            ]
        );

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_can_not_create_credit_card_data_with_invalid_holder_name()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                '',
                '6011111111111117',
                123,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_can_not_create_credit_card_data_with_invalid_cvv()
    {
        $now = new \DateTime();

        $this->beConstructedThrough(
            'fromRawValues',
            [
                'John Doe',
                '6011111111111117',
                1,
                (int) $now->format('y') + 1,
                (int) $now->format('m'),
            ]
        );

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
