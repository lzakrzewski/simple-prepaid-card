Feature: Unblock funds on a credit card

  Scenario: Unblock funds on a blocked credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "50" GBP
     When I unblock funds on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were unblocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP

  Scenario: Unblock funds on a credit card when funds are not blocked
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "70" GBP
     When I unblock funds on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should not be notified that funds were unblocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP

  Scenario: Unblock funds on a not existing credit card
    Given I don't have a credit card
     When I unblock funds on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that credit card does not exist
