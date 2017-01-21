<?php

declare(strict_types=1);

namespace tests\contexts;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

class DefaultContext implements KernelAwareContext, SnippetAcceptingContext
{
    use KernelDictionary;
}
