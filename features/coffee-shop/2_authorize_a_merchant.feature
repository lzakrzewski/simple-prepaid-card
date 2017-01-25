@incomplete
Feature: Authorize a merchant

  Scenario: Authorize a merchant
    Given I am a merchant with id "abcd" authorized for "10" GBP
     When I authorize merchant with id "abcd" for "100" GBP
     Then I should be notified that merchant was authorized
      And I should be authorized for "110" GBP

  Scenario: Authorize a not existing merchant
     When I authorize merchant with id "abcd" for "100" GBP
     Then I should be notified that merchant does not exist
      And I should not be notified that merchant was authorized