<?php

declare(strict_types=1);

namespace tests\contexts;

use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use SimpleBus\Message\Bus\MessageBus;
use tests\builders\Builder;
use tests\testServices\ContainsRecordedEventsMiddleware;

class DefaultContext implements KernelAwareContext, SnippetAcceptingContext
{
    use KernelDictionary;

    /** @var \Exception[] */
    private $exceptions = [];

    public function handle($command)
    {
        try {
            $this->commandBus()->handle($command);
        } catch (\Exception $e) {
            $this->exceptions[] = $e;
        }
    }

    protected function buildPersisted(Builder $builder)
    {
        $object = $builder->build();

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $em->persist($object);
        $em->flush();
    }

    private function commandBus(): MessageBus
    {
        return $this->getContainer()->get('command_bus');
    }

    protected function expectEvent(string $eventClass)
    {
        $events = $this->filterByClassName(
            $this->events()->recordedEvents(),
            $eventClass
        );

        Assertion::greaterThan(count($events), 0, sprintf('Expected at least one event of class %s', $eventClass));
        Assertion::allIsInstanceOf($events, $eventClass);
    }

    protected function expectsNoEvents()
    {
        Assertion::count($this->events()->recordedEvents(), 0, 'Expected no recorded events');
    }

    protected function expectException(string $exceptionClass)
    {
        $exceptions = $this->filterByClassName(
            $this->exceptions,
            $exceptionClass
        );

        Assertion::greaterThan(count($exceptions), 0, sprintf('Expected at least one exception of class %s', $exceptionClass));
        Assertion::allIsInstanceOf($exceptions, $exceptionClass);
    }

    private function filterByClassName(array $elements, string $expectedClass): array
    {
        return array_filter($elements, function ($event) use ($expectedClass) {
            return $event instanceof $expectedClass;
        });
    }

    private function events(): ContainsRecordedEventsMiddleware
    {
        return $this->getContainer()->get('simple_prepaid_card.command_bus.recorded_events');
    }
}
