@incomplete
Feature: Create credit card

  Scenario: Create a credit card
    Given I don't have a credit card
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and name "John Doe"
     Then I should be notified that credit card was created
      And balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "0"
      And available balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "0"

  Scenario: Create a credit card twice
    Given I have a credit card with id "e6eb2b4c-94ce-46eb-b01c-67ed491dad26"
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and name "John Doe"
     Then I should be notified that credit card was already created