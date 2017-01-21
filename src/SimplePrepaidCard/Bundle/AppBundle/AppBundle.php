<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle;

use SimplePrepaidCard\Bundle\AppBundle\DependencyInjection\SimplePrepaidCardAppExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /** {@inheritdoc} */
    public function getContainerExtension(): ExtensionInterface
    {
        return new SimplePrepaidCardAppExtension();
    }
}
