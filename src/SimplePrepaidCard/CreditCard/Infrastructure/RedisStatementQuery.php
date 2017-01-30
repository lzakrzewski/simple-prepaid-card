<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Infrastructure;

use Money\Currency;
use Money\Money;
use Predis\Client as RedisClient;
use Predis\Pipeline\Pipeline;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CreditCard\Application\Query\StatementQuery;
use SimplePrepaidCard\CreditCard\Application\Query\StatementView;

class RedisStatementQuery implements StatementQuery
{
    /** @var RedisClient */
    private $redis;

    public function __construct(RedisClient $redis)
    {
        $this->redis = $redis;
    }

    /** {@inheritdoc} */
    public function get(UuidInterface $creditCardId): array
    {
        $pipeline = $this->redis->pipeline();
        foreach ($this->redis->zrevrange($this->statementKey($creditCardId), 0, -1) as $key) {
            $pipeline->get($key);
        }

        return $this->mapResult($pipeline);
    }

    private function mapResult(Pipeline $pipeline): array
    {
        return array_map(function (string $content) {
            return $this->deserialize($content);
        }, $pipeline->execute());
    }

    private function deserialize(string $content): StatementView
    {
        $postData = json_decode($content, true);

        return new StatementView(
            Uuid::fromString($postData['creditCardId']),
            Uuid::fromString($postData['holderId']),
            new \DateTime($postData['at']),
            $postData['reason'],
            new Money($postData['amount']['amount'], new Currency($postData['amount']['currency'])),
            new Money($postData['balance']['amount'], new Currency($postData['balance']['currency'])),
            new Money($postData['availableBalance']['amount'], new Currency($postData['availableBalance']['currency']))
        );
    }

    private function statementKey(UuidInterface $creditCardId): string
    {
        return sprintf('%s:%s', RedisStatementProjector::REDIS_KEY, $creditCardId);
    }
}
