Feature: Authorize a merchant

  Scenario: Authorize a merchant
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "10" GBP
      And there is a customer with id "5a29e675-1c05-4323-ae72-9ffbbb17ad38"
     When A customer authorizes a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" to "100" GBP
     Then I should be notified that merchant was authorized
      And I should be authorized to "110" GBP

  Scenario: Authorize a merchant to negative amount
    Given I am a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" authorized to "10" GBP
     And there is a customer with id "5a29e675-1c05-4323-ae72-9ffbbb17ad38"
     When A customer authorizes a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" to "-100" GBP
     Then I should be notified that I cannot use negative amount
      And I should not be notified that merchant was authorized
      And I should be authorized to "10" GBP

  Scenario: Authorize a not existing merchant
      And there is a customer with id "5a29e675-1c05-4323-ae72-9ffbbb17ad38"
     When A customer authorizes a merchant with id "49ce95dc-bb15-4c45-9df4-7b8c0a9f8896" to "100" GBP
     Then I should be notified that merchant does not exist
      And I should not be notified that merchant was authorized