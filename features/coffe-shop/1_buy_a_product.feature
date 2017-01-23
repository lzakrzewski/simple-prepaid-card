@incomplete
Feature: Buy a product

  Scenario: Buy a coffee in coffee shop when credit card provider will approve authorization request
    Given I am a customer with id "abc"
      And there is a merchant with id "qwe"
      And credit card provider will approve authorization request for "100" GBP
     When I buy a product "coffee" for "100" GBP
     Then I should be notified that product was bought
      And I should be notified that merchant was authorized

  Scenario: Buy a coffee in coffee shop when credit card provider will decline authorization request
    Given I am a customer with id "abc"
      And there is a merchant with id "qwe"
      And credit card provider will decline authorization request for "100" GBP
     When I buy a product "coffee" for 100 GBP
     Then I should be notified that authorization request was declined
      And I should not be notified that product was bought
      And I should not be notified that merchant was authorized

  Scenario: Buy a coffee in coffee shop as not existing customer
    Given there is a merchant with id "qwe"
      And credit card provider will approve authorization request for "100" GBP
     When I buy a product "coffee" for 100 GBP
     Then I should be notified that customer does not exist

  Scenario: Buy an unknown product
    Given I am a customer with id "abc"
      And there is a merchant with id "qwe"
      And credit card provider will approve authorization request for "100" GBP
     When I buy a product "unknown" for 100 GBP
     Then I should be notified that product is unknown