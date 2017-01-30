<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('simple-credit-card:setup-data')
            ->setDescription('Adds default Customer and Merchant objects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearDatabase();
        $this->coffeeShopFixtures($output);
    }

    private function coffeeShopFixtures(OutputInterface $output)
    {
        $this->getContainer()
            ->get('simple_prepaid_card.coffee_shop.repository.customer')
            ->add($customer = Customer::create());

        $this->getContainer()
            ->get('simple_prepaid_card.coffee_shop.repository.merchant')
            ->add($merchant = Merchant::create());

        $this->entityManager()->flush();

        $output->writeln(sprintf('Customer with id <info>"%s"</info> was added.', $customer->customerId()));
        $output->writeln(sprintf('Merchant with id <info>"%s"</info> was added.', $merchant->merchantId()));
    }

    private function entityManager(): EntityManager
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    private function clearDatabase()
    {
        $entityManager = $this->entityManager();

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($entityManager->getMetadataFactory()->getAllMetadata());
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }
}
