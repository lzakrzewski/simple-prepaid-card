Feature: Block funds on a credit card

  Scenario: Block whole funds on a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "100" GBP and available balance "100" GBP
     When I block "100" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were blocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "100" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "0" GBP

  Scenario: Block partial funds on a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "50" GBP
     When I block "49" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were blocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "1" GBP

  Scenario: Block funds on a credit card when not enough funds on credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "100" GBP and available balance "99" GBP
     When I block "100" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that I can not block more than available funds

  Scenario: Block funds on a not existing credit card
    Given I don't have a credit card
     When I block "49" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that credit card does not exist
