Feature: Refund captured

  Scenario: Refund whole captured when credit card provider will approve refund
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "0" GBP and "100" GBP captured
      And credit card provider will approve refund
     When I refund "100" GBP from my captured
     Then I should be notified that captured was refunded
      And I should be authorized to "0" GBP
      And I should have captured "0" GBP

  Scenario: Refund partial captured when credit card provider will approve refund
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "10" GBP and "100" GBP captured
      And credit card provider will approve refund
     When I refund "50" GBP from my captured
     Then I should be notified that captured was refunded
      And I should be authorized to "10" GBP
      And I should have captured "50" GBP

  Scenario: Refund captured with negative amount
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "10" GBP and "100" GBP captured
      And credit card provider will approve refund
     When I refund "-50" GBP from my captured
     Then I should be notified that I cannot use negative amount
      And I should not be notified that captured was refunded
      And I should be authorized to "10" GBP
      And I should have captured "100" GBP

  Scenario: Refund authorization when credit card provider will decline refund
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "0" GBP and "100" GBP captured
      And credit card provider will decline refund
     When I refund "100" GBP from my captured
     Then I should be notified that refund was declined
      And I should not be notified that captured was refunded
      And I should be authorized to "0" GBP
      And I should have captured "100" GBP

  Scenario: Refund authorization as not existing merchant
    Given credit card provider will approve refund
     When I refund "100" GBP from my captured
     Then I should not be notified that merchant does not exist
      And I should not be notified that captured was refunded