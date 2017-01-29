<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SimplePrepaidCardAppExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/CreditCard'));
        $loader->load('command_handlers.yml');
        $loader->load('queries.yml');
        $loader->load('repositories.yml');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/CoffeeShop'));
        $loader->load('command_handlers.yml');
        $loader->load('domain_services.yml');
        $loader->load('queries.yml');
        $loader->load('repositories.yml');
        $loader->load('subscribers.yml');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('session.yml');
        $loader->load('twig.yml');
    }

    /** {@inheritdoc} */
    public function getAlias()
    {
        return 'simple_prepaid_card';
    }
}
