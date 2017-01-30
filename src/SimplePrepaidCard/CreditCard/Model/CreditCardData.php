<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Assert\Assertion;
use SimplePrepaidCard\Common\Model\ValueObject;

final class CreditCardData implements ValueObject
{
    /** @var string */
    private $holder;

    /** @var string */
    private $number;

    /** @var string */
    private $cvv;

    /** @var \DateTime */
    private $expiryDate;

    private function __construct(string $holder, string $number, int $cvv, \DateTime $expiryDate)
    {
        $this->holder     = $this->validateHolder($holder);
        $this->number     = $this->validateNumber($number);
        $this->cvv        = $this->validateCvv($cvv);
        $this->expiryDate = $this->validateExpirationDate($expiryDate);
    }

    public static function fromRawValues(string $holder, string $number, int $cvv, int $expiryDateYear, int $expiryDateMonth)
    {
        return new self(
            $holder,
            $number,
            $cvv,
            self::expirationDateFromRawValues($expiryDateYear, $expiryDateMonth)
        );
    }

    public function expirationDate(): \DateTime
    {
        return $this->expiryDate;
    }

    public function holder(): string
    {
        return $this->holder;
    }

    public function number(): string
    {
        return $this->number;
    }

    public function cvv(): string
    {
        return $this->cvv;
    }

    private static function expirationDateFromRawValues(int $expiryDateYear, int $expiryDateMonth): \DateTime
    {
        Assertion::between($expiryDateMonth, 1, 12);

        $expirationDate = \DateTime::createFromFormat(
            'Y-m',
            sprintf(
                '20%02d-%02d',
                $expiryDateYear,
                $expiryDateMonth
            )
        );

        if (false === $expirationDate) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expiration date "%02d-%02d" is invalid',
                    $expiryDateYear,
                    $expiryDateMonth
                )
            );
        }

        return $expirationDate;
    }

    private function validateExpirationDate(\DateTime $expiryDate): \DateTime
    {
        $now        = (new \DateTime())->modify('last day of this month');
        $expiryDate = $expiryDate->modify('last day of this month');

        if ($now > $expiryDate) {
            throw new \InvalidArgumentException('Expiration date can not be from past.');
        }

        return $expiryDate;
    }

    private function validateHolder(string $holder): string
    {
        Assertion::notBlank($holder);

        return $holder;
    }

    private function validateNumber(string $number)
    {
        $number = trim(preg_replace('/\s+/', '', $number));

        Assertion::minLength($number, 12);
        Assertion::maxLength($number, 19);
        Assertion::numeric($number);

        return md5($number);
    }

    private function validateCvv(int $cvv): string
    {
        Assertion::length((string) $cvv, 3);

        return md5((string) $cvv);
    }
}
