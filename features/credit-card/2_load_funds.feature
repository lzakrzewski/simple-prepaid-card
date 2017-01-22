@incomplete
Feature: Load funds onto a card

  Scenario: Load funds onto a card
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     When I load "100" GBP funds onto a card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that funds were loaded
      And balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "100"
      And available balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "100"

  Scenario: Load funds onto not existing card
    Given I don't have a credit card
     When I load "100" GBP funds onto a card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     Then I should be notified that credit card does not exist