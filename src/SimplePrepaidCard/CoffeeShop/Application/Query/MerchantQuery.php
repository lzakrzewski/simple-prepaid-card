<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Application\Query;

interface MerchantQuery
{
    public function get(): MerchantView;
}
