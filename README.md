# simple-prepaid-card
[![Build Status](https://travis-ci.org/lzakrzewski/simple-prepaid-card.svg?branch=master)](https://travis-ci.org/lzakrzewski/simple-prepaid-card)

This repository is Proof of concept (POC) to demonstrate a simplified model of a prepaid card. The card holds a balance in GBP and customers can make transactions in GBP.

## Sandbox
Sandbox is available here: [http://138.68.141.70/](http://138.68.141.70/). At any time user can reset all sandbox data using a red button from navbar.

## Design and Architecture

#### DDD and Bounded contexts
The application was split for 2 main bounded contexts:  

CoffeeShop:
 - Contains **Merchant** and **Customer** aggregates and their behavior (buy a product by Customer, authorize a Merchant, capture an authorization etc.)
 
CreditCard:
 - Contains **CreditCard** aggregate and behavior (load/block/unblock/charge funds)
 
It's highly possible that in future the responsibility of CreditCard will by moved to an external 3rd party credit card provider like ([https://stripe.com](Stripe), [https://www.braintreepayments.com/](Braintree)), so in order to easy switch **CreditCardProvider**  the CreditCard context was completely decoupled from CoffeeShop.
CoffeeShop context has an interface **CreditCardProvider** and current implementation **LocalCreditCardProvider** which is a bridge to CreditCard context. If the external **CreditCardProvider** will be chosen then **CreditCardProvider** need to have another implementation like **StripeCreditCardProvider**.

Aggregates were marked with `Aggregate` interface, Value objects with `ValueObject` interface and `

#### Layered Application Architecture
Each context of that application (CoffeeShop, CreditCard) has layers:
- `Application` - This layer coordinates the application activity.
- `Infrastructure` - This layer handles interaction with infrastructure. It contains an implementation of adapters to an infrastructure.
- `Model` - It handles business rules related to the context. It contains aggregates which trigger domain events

#### CQRS and single responsibility
I used [SimpleBus/MessageBus](http://simplebus.github.io/MessageBus/) to implement CQRS pattern. 
Each command has only one responsibility and those responsibilities were described with `Gherkin scenarios`:
 

Read model and write model are completely separated. To expose that fact the write model was realized with sqlite database and read model with redis.

#### Hexagonal architecture
Each adapter to an infrastructure has an interface extracted and implementation within `Infrastructure` layer. 
Examples:
`StatementQuery` => `RedisStatementQuery`  
`CustomerRepository` => `DoctrineORMRepository`  
`CreditCardProvider` => `LocalCreditCardProvider`  

It allows me to quickly switch between implementations in case when for eq. I decide to use `DoctrineODM` instead of `DoctrineORM`. It is helpful for test purposes as well. Here is another implementation od credit card provider `TestCreditCardProvider` which allows me to define behavior of credit card provider for test purposes.

#### Coupling with framework
The application has framework agnostic model. Entry point for the application model is Symfony controller within the Bundle. The bundle contains only framework related stuff (views) and configuration of them.

## Provisioning and Deployment
Here is a simple script to firstly provision http://138.68.141.70/ host with Ansible and then deploy the application on it.
Each deployment is triggered in automated way after each successful build with travis. I used encrypted ssh key to allow Travis CI deploy my host.

## Testing
An application has wide testsuite:
- composer static-analysis (php-cs-fixer was used to fix automaticly the broken code standards)
- composer spec (PHPSpec was used specification testing of business model)
- composer integration (PHPUnit was used for test integration with database, redis cache and framework)
- composer behat (Behat was used for acceptance test of business requirements)
- composer e2e Its a testsuite simulates using whole application on production. It's for ensuring that everything is able to work together (framework, two bounded contexts etc)






