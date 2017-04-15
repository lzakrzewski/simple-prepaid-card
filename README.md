# simple-prepaid-card
[![Build Status](https://travis-ci.org/lzakrzewski/simple-prepaid-card.svg?branch=master)](https://travis-ci.org/lzakrzewski/simple-prepaid-card)

This repository is Proof of concept (POC) to demonstrate a simplified model of a prepaid card. The card holds a balance in GBP and customers can make transactions in GBP.

## Sandbox
Sandbox is available here: [http://178.62.42.204/](http://178.62.42.204/). At any time user can reset all sandbox data using a red button from navbar.

## Design and Architecture

#### DDD and Bounded contexts
As in real life, the **Customer** and the **Merchant** in the shop are not focused how the **CreditCardProvider** deals with the transactions on card internally.
So, the application was split for 2 main bounded contexts, which are independent individually:

CoffeeShop:
 - Contains **Merchant** and **Customer** aggregates and their behavior (buy a product by Customer, authorize a Merchant, capture an authorization etc.)
 
CreditCard:
 - Contains **CreditCard** aggregate and behavior (load/block/unblock/charge funds)
 
It's highly possible that in future the responsibility of CreditCard will by moved to an external 3rd party credit card provider like ([https://stripe.com](Stripe), [https://www.braintreepayments.com/](Braintree)).  
In order to easy switch **CreditCardProvider**  the CreditCard context was completely decoupled from CoffeeShop context.  
CoffeeShop context has an interface `CreditCardProvider` and current implementation is `LocalCreditCardProvider` which is a bridge to CreditCard context.   
If the external `CreditCardProvider` will be chosen then `CreditCardProvider` need to have another implementation like `StripeCreditCardProvider`.  

- Aggregates were marked with `SimplePrepaidCard\Common\Model\Aggregate` interface,   
- Value objects with `SimplePrepaidCard\Common\Model\ValueObject` interface,  
- Domain events with `SimplePrepaidCard\Common\Model\DomainEvent` interface.  

#### Layered Application Architecture
Each context of that application (CoffeeShop, CreditCard) has layers:
- `Application` - This layer coordinates the application activity.
- `Infrastructure` - This layer handles interaction with infrastructure. It contains an implementation of adapters to an infrastructure.
- `Model` - It handles business rules related to the context. It contains aggregates which trigger domain events.

#### CQRS and single responsibility
I used [SimpleBus/MessageBus](http://simplebus.github.io/MessageBus/) to implement **CQRS** pattern. 
Each command has only one responsibility and those responsibilities were described with `Gherkin` scenarios:

CoffeeShop context commands:
- [Buy a product](features/coffee-shop/1_buy_a_product.feature)
- [Authorize a merchant](features/coffee-shop/2_authorize_a_merchant.feature)
- [Capture an authorization](features/coffee-shop/3_capture_an_authorization.feature)
- [Reverse an authorization](features/coffee-shop/4_reverse_an_authorization.feature)
- [Refund captured](features/coffee-shop/5_refund_captured.feature)

CreditCard context commands:
- [Create a credit card](features/credit-card/1_create_a_credit_card.feature)
- [Load funds](features/credit-card/2_load_funds.feature)
- [Block funds](features/credit-card/3_block_funds.feature)
- [Unblock funds](features/credit-card/4_unblock_funds.feature)
- [Charge funds](features/credit-card/5_charge_funds.feature)
 
**Read model** and **write model** are completely separated.  
To expose that fact the write model was realized with **Sqlite** database and read model with **Redis** cache.

#### Hexagonal architecture
Each adapter to an infrastructure has an interface extracted and implementation within `Infrastructure` layer.    
Examples:   
`StatementQuery` => `RedisStatementQuery`  
`CustomerRepository` => `DoctrineORMRepository`  
`CreditCardProvider` => `LocalCreditCardProvider`   

It allows me to quickly switch between implementations in a case when I decide for e.g. to use `DoctrineODM` instead of `DoctrineORM`.   
It is helpful for test purposes as well. Here is another implementation od credit card provider `TestCreditCardProvider` which allows me to define a behavior of credit card provider for test purposes.

#### Coupling with framework
The application has framework agnostic model. The entry point for the application model is Symfony controller within the Bundle. The bundle contains only framework related stuff (views) and configuration of them.

## Provisioning and Deployment
Here is a simple script to firstly provision http://178.62.42.204/ host with [Ansible](https://www.ansible.com/) and then deploy the application on it.
Each deployment is triggered in automated way after each successful build with **Travis CI**. I used encrypted ssh key to allow **Travis CI** deploy my host.

## Testing
An application has wide test-suite:
- `composer static-analysis` - (php-cs-fixer was used to fix automatically the broken code standards)
- `composer spec` - (PHPSpec was used specification testing of business model)
- `composer integration` - (PHPUnit was used for test integration with **Sqlite** database, **Redis** cache and **Symfony** framework)
- `composer behat` - (Behat was used for acceptance test of business requirements)
- `composer e2e` - It's a test-suite that simulates using a whole application on production. It's for ensuring that everything is able to work together (framework, two bounded contexts etc)






