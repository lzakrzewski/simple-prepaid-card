<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table
 */
final class StatementView
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     */
    public $creditCardId;

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     */
    public $holderId;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime")
     */
    public $date;

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     */
    public $description;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="\Money\Money")
     */
    public $amount;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="\Money\Money")
     */
    public $availableBalance;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="\Money\Money")
     */
    public $balance;

    public function __construct(int $id = null, UuidInterface $creditCardId, UuidInterface $holderId, \DateTime $date, string $description, Money $amount, Money $availableBalance, Money $balance)
    {
        $this->id               = $id;
        $this->creditCardId     = $creditCardId;
        $this->holderId         = $holderId;
        $this->date             = $date;
        $this->description      = $description;
        $this->amount           = $amount;
        $this->availableBalance = $availableBalance;
        $this->balance          = $balance;
    }
}
