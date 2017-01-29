<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Command;

use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\CustomerRepository;
use SimplePrepaidCard\CoffeeShop\Model\Products;

final class BuyProductHandler
{
    /** @var CustomerRepository */
    private $customers;

    /** @var Products */
    private $products;

    /** @var CreditCardProvider */
    private $creditCardProvider;

    public function __construct(CustomerRepository $customers, Products $products, CreditCardProvider $creditCardProvider)
    {
        $this->customers          = $customers;
        $this->products           = $products;
        $this->creditCardProvider = $creditCardProvider;
    }

    public function handle(BuyProduct $command)
    {
        $customer = $this->customers->get($command->customerId);
        $customer->buyProduct($this->products->get($command->productId), $this->creditCardProvider);
    }
}
