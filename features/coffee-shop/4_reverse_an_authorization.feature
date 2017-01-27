Feature: Reverse authorization

  Scenario: Reverse whole authorization when credit card provider will approve reverse
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "0" GBP captured
      And credit card provider will approve reverse
     When I reverse "100" GBP from my authorization
     Then I should be notified that authorization was reversed
      And I should be authorized to "0" GBP
      And I should have captured "0" GBP

  Scenario: Reverse partial authorization when credit card provider will approve reverse
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "10" GBP captured
      And credit card provider will approve reverse
     When I reverse "50" GBP from my authorization
     Then I should be notified that authorization was reversed
      And I should be authorized to "50" GBP
      And I should have captured "10" GBP

  Scenario: Reverse an authorization when credit card provider will decline reverse
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "0" GBP captured
      And credit card provider will decline reverse
     When I reverse "100" GBP from my authorization
     Then I should be notified that reverse was declined
      And I should not be notified that authorization was reversed
      And I should be authorized to "100" GBP
      And I should have captured "0" GBP

  Scenario: Reverse an authorization with negative amount
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "0" GBP captured
      And credit card provider will approve reverse
     When I reverse "-100" GBP from my authorization
     Then I should be notified that I cannot use negative amount
      And I should not be notified that authorization was reversed
      And I should be authorized to "100" GBP
      And I should have captured "0" GBP

  Scenario: Reverse an authorization as not existing merchant
    Given credit card provider will approve reverse
     When I reverse "100" GBP from my authorization
     Then I should not be notified that merchant does not exist
      And I should not be notified that authorization was reversed