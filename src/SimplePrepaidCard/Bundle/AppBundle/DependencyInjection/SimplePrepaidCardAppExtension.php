<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class SimplePrepaidCardAppExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $config, ContainerBuilder $container)
    {
    }

    /** {@inheritdoc} */
    public function getAlias()
    {
        return 'simple_prepaid_card';
    }
}
