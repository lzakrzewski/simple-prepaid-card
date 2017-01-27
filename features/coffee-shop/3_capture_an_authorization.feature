Feature: Capture an authorization

  Scenario: Capture whole authorization when credit card provider will approve capture
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "0" GBP captured
      And credit card provider will approve capture
     When I capture "100" GBP from my authorization
     Then I should be notified that authorization was captured
      And I should be authorized to "0" GBP
      And I should have captured "100" GBP

  Scenario: Capture partial authorization when credit card provider will approve capture
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "10" GBP captured
      And credit card provider will approve capture
     When I capture "50" GBP from my authorization
     Then I should be notified that authorization was captured
      And I should be authorized to "50" GBP
      And I should have captured "60" GBP

  Scenario: Capture more than authorized
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "10" GBP captured
      And credit card provider will approve capture
     When I capture "150" GBP from my authorization
     Then I should be notified that I can not capture more than authorized
      And I should be authorized to "100" GBP
      And I should have captured "10" GBP

  Scenario: Capture an authorization when credit card provider will decline capture
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "99" GBP and "0" GBP captured
      And credit card provider will decline capture
     When I capture "100" GBP from my authorization
     Then I should not be notified that authorization was captured
      And I should be authorized to "99" GBP
      And I should have captured "0" GBP

  Scenario: Capture an authorization with negative amount
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "100" GBP and "10" GBP captured
      And credit card provider will approve capture
     When I capture "-100" GBP from my authorization
     Then I should be notified that I cannot use negative amount
      And I should be authorized to "100" GBP
      And I should have captured "10" GBP

  Scenario: Capture an authorization as not existing merchant
    Given credit card provider will approve capture
     When I capture "100" GBP from my authorization
     Then I should not be notified that merchant does not exist