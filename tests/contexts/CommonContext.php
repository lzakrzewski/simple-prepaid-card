<?php

declare(strict_types=1);

namespace tests\contexts;

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
}
