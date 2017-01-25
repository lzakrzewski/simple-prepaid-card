<?php

declare(strict_types=1);

namespace tests\contexts;

use Money\Money;

class CommonContext extends DefaultContext
{
    /** @BeforeScenario */
    public function testSetup()
    {
        $this->getContainer()->get('test_setup')->setup();
    }

    /**
     * @Then /^I should not be notified that (.*)$/
     */
    public function noEvents()
    {
        $this->expectsNoEvents();
    }

    /**
     * @Transform :amount
     */
    public function money(string $money): Money
    {
        return Money::GBP((int) $money);
    }
}
