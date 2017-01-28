<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Command;

use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCoffeeShopDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('simple-credit-card:setup-coffee-shop-data')
            ->setDescription('Adds default Customer and Merchant objects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()
            ->get('simple_prepaid_card.coffee_shop.repository.customer')
            ->add($customer = Customer::create());

        $this->getContainer()
            ->get('simple_prepaid_card.coffee_shop.repository.merchant')
            ->add($merchant = Merchant::create());

        $this->getContainer()
            ->get('doctrine.orm.default_entity_manager')
            ->flush();

        $output->writeln(sprintf('Customer with id <info>"%s"</info> was added.', $customer->customerId()));
        $output->writeln(sprintf('Merchant with id <info>"%s"</info> was added.', $merchant->merchantId()));
    }
}
