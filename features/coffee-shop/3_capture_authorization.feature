@incomplete
Feature: Capture authorization

  Scenario: Capture whole authorization when credit card provider will approve capture
    Given I am a merchant with id "abcd" authorized to "100" GBP
      And credit card provider will approve capture "100" GBP
     When I capture "100 GBP" from my authorization
     Then I should be notified that authorization was captured
      And I should be authorized to "0" GBP
      And I should have captured "100" GBP

  Scenario: Capture partial authorization when credit card provider will approve capture
    Given I am a merchant with id "abcd" authorized to "100" GBP
      And credit card provider will approve capture "50" GBP
     When I capture "50 GBP" from my authorization
     Then I should be notified that authorization was captured
      And I should be authorized to "50" GBP
      And I should have captured "50" GBP

  Scenario: Capture authorization when credit card provider will decline capture
    Given I am a merchant with id "abcd" authorized to "99" GBP
      And credit card provider will decline capture "100" GBP
     When I capture "100 GBP" from my authorization
     Then I should not be notified that authorization was captured
      And I should be authorized to "99" GBP
      And I should have captured "0" GBP

  Scenario: Capture authorization as not existing merchant
    Given credit card provider will approve capture "100" GBP
     When I capture "100 GBP" from my authorization
     Then I should not be notified that merchant does not exist