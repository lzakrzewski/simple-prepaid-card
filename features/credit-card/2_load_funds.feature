Feature: Load funds onto a credit card
#todo: Negative handle
  Scenario: Load funds onto a empty credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "0" GBP
     When I load "100" GBP onto a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were loaded
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "100" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "100" GBP

  Scenario: Load funds onto a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "100" GBP
     When I load "100" GBP onto a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were loaded
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "200" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "200" GBP

  Scenario: Load negative funds onto a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "100" GBP
     When I load "100" GBP onto a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were loaded
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "200" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "200" GBP

  Scenario: Load funds onto not existing card
    Given I don't have a credit card
     When I load "100" GBP onto a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that credit card does not exist
