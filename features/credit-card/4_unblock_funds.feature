@incomplete
Feature: Unblock funds on a credit card

  Scenario: Unblock funds on a credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with available balance "100" GBP
     When I block "100" GBP funds on a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7"
     Then I should be notified that founds on a credit card were blocked
      And balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "100" GBP
      And available balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "0" GBP

  Scenario: Unblock funds on a credit card when not enough funds on credit card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26" with available balance "99" GBP
     When I block "100" GBP funds on a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7"
     Then I should be notified that not enough funds on a credit card

  Scenario: Unblock funds on a not existing credit card
    Given I don't have a credit card
     When I block "100" GBP funds on a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7"
     Then I should be notified that founds on a credit card does not exist