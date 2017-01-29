Feature: Create a credit card
  Scenario: Create a credit card
    Given I don't have a credit card
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and data:
        | holder   | number           | cvv | expiryDateYear | expiryDateMonth |
        | John Doe | 4111111111111111 | 111 | 25             | 01              |
     Then I should be notified that a credit card was created
      And balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "0" GBP
      And available balance of a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" should be "0" GBP

  Scenario: Create a credit card twice
    Given I have a credit card with id "27cd24c0-4d45-4c86-bd88-3a70c2cee8b5"
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and data:
        | holder      | number           | cvv | expiryDateYear | expiryDateMonth |
        | John Doe II | 4111111111111111 | 111 | 25             | 01              |
     Then I should be notified that a credit card was created

  Scenario: Create a credit card with invalid data
    Given I don't have a credit card
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and data:
        | holder   | number | cvv | expiryDateYear | expiryDateMonth |
        | John Doe | 4111   | 111 | 25             | 01              |
     Then I should be notified that a credit card data is invalid
      And I should not be notified that a credit card was created

  Scenario: Create a credit card with same id twice
    Given I have a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7"
     When I create a credit card with id "6a45032e-738a-48b7-893d-ebdc60d0c3b7" and data:
        | holder      | number           | cvv | expiryDateYear | expiryDateMonth |
        | John Doe II | 4111111111111111 | 111 | 25             | 01              |
     Then I should be notified that credit card already exist
      And I should not be notified that a credit card was created
