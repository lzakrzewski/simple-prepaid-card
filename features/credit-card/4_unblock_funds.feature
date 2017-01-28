Feature: Unblock funds on a credit card

  Scenario: Unblock whole funds on a blocked credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "0" GBP
     When I unblock "70" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were unblocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP

  Scenario: Unblock partial funds on a blocked credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "50" GBP
     When I unblock "10" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were unblocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "60" GBP

  Scenario: Unblock too much funds on a blocked credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "50" GBP
     When I unblock "99" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were unblocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP

  Scenario: Unblock negative funds on a blocked credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "50" GBP
     When I unblock "-10" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that I can not use negative funds
      And I should not be notified that funds were unblocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "50" GBP

  Scenario: Unblock funds on a credit card when funds are not blocked
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with balance "70" GBP and available balance "70" GBP
     When I unblock "100" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should not be notified that funds were unblocked
      And balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP
      And available balance of a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" should be "70" GBP

  Scenario: Unblock funds on a not existing credit card
    Given I don't have a credit card
     When I unblock "100" GBP on a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that credit card does not exist
      And I should not be notified that funds were unblocked
