@incomplete
Feature: Reverse authorization

  Scenario: Reverse whole authorization when credit card provider will approve reserve
    Given I am a merchant with id "abcd" authorized for "100" GBP
      And credit card provider will approve reverse "100" GBP
     When I reverse "100 GBP" from my authorization
     Then I should be notified that authorization was captured
      And I should be authorized for "0" GBP
      And I should have captured "0" GBP

  Scenario: Reverse partial authorization when credit card provider will approve reserve
    Given I am a merchant with id "abcd" authorized for "100" GBP
      And credit card provider will approve reverse "50" GBP
     When I reverse "50" GBP from my authorization
     Then I should be notified that authorization was captured
      And I should be authorized for "50" GBP
      And I should have captured "0" GBP

  Scenario: Reverse authorization when credit card provider will decline reserve
    Given I am a merchant with id "abcd" authorized for "100" GBP
      And credit card provider will decline reverse "100" GBP
     When I reserve "100 GBP" from my authorization
     Then I should not be notified that authorization was captured
      And I should be authorized for "100" GBP
      And I should have captured "0" GBP

  Scenario: Reverse authorization as not existing merchant
    Given credit card provider will approve reverse "100" GBP
     When I reserve "100 GBP" from my authorization
     Then I should not be notified that merchant does not exist