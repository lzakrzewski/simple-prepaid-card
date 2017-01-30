<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Predis\Client as RedisClient;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\Common\Model\DomainEvent;
use SimplePrepaidCard\CreditCard\Application\Query\StatementProjector;
use SimplePrepaidCard\CreditCard\Model\FundsWereCharged;
use SimplePrepaidCard\CreditCard\Model\FundsWereLoaded;

class RedisStatementProjector implements StatementProjector
{
    const REDIS_KEY = 'statement';

    /** @var $redis */
    private $redis;

    public function __construct(RedisClient $redis)
    {
        $this->redis = $redis;
    }

    public function applyThatFundsWereLoaded(FundsWereLoaded $event)
    {
        $pipeline = $this->redis->pipeline();
        $key      = Uuid::uuid4();

        $pipeline->zadd($this->statementKey($event->creditCardId()), $event->at()->getTimestamp(), $key)
            ->set($key, $this->serialize($event))
            ->execute();
    }

    public function applyThatFundsWereCharged(FundsWereCharged $event)
    {
        $pipeline = $this->redis->pipeline();
        $key      = Uuid::uuid4();

        $pipeline->zadd($this->statementKey($event->creditCardId()), $event->at()->getTimestamp(), $key)
            ->set($key, $this->serialize($event))
            ->execute();
    }

    private function serialize(DomainEvent $event): string
    {
        return json_encode([
            'creditCardId'     => $event->creditCardId()->toString(),
            'holderId'         => $event->holderId()->toString(),
            'at'               => $event->at()->format('Y-m-d H:i:s'),
            'reason'           => $event->reason(),
            'amount'           => $event->amount()->jsonSerialize(),
            'availableBalance' => $event->availableBalance()->jsonSerialize(),
            'balance'          => $event->balance()->jsonSerialize(),
        ]);
    }

    private function statementKey(UuidInterface $creditCardId): string
    {
        return sprintf('%s:%s', self::REDIS_KEY, $creditCardId);
    }
}
