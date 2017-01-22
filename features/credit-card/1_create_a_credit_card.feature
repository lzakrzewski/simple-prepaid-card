Feature: Create a credit card

  Scenario: Create a credit card
    Given I don't have a credit card
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and holder name "John Doe"
     Then I should be notified that a credit card was created
      And balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "0"
      And available balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "0"

  Scenario: Create a credit card with same id twice
    Given I have a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7"
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and holder name "John Doe"
     Then I should be notified that credit card already exist