@incomplete
Feature: Charge funds from a credit card

  Scenario: Charge whole funds from a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "50" GBP and available balance "0" GBP
     When I charge "50" GBP from a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were charged
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "0" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "0" GBP

  Scenario: Charge partial funds from a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "100" GBP and available balance "50" GBP
     When I charge "49" GBP from a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were charged
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "51" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "1" GBP

  Scenario: Charge too much funds from a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "100" GBP and available balance "50" GBP
     When I charge "51" GBP from a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that I can not charge too much funds
     Then I should not be notified that funds were charged
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "100" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "50" GBP

  Scenario: Charge funds from a not existing card
    Given I don't have a credit card
     When I charge "49" GBP from a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that credit card does not exist