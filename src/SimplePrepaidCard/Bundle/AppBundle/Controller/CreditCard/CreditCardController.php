<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use SimplePrepaidCard\Bundle\AppBundle\Form\CreditCardType;
use SimplePrepaidCard\CreditCard\Application\Command\CreateCreditCard;
use SimplePrepaidCard\CreditCard\Application\Command\LoadFunds;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//Todo: Flashbag magic
class CreditCardController extends Controller
{
    /**
     * @Config\Route("/create-credit-card", name="create-credit-card")
     * @Config\Method({"GET", "POST"})
     */
    public function createCreditCardAction(Request $request)
    {
        $form = $this->createForm(CreditCardType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('command_bus')->handle(
                new CreateCreditCard(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'), Uuid::uuid4(), $form->getData()['card_holder'])
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/credit-card/create-credit-card.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/load-funds", name="load-funds")
     * @Config\Method({"GET", "POST"})
     */
    public function loadFundsAction(Request $request)
    {
        $form = $this->createForm(CreditCardType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('command_bus')->handle(
                new LoadFunds(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7'), $form->getData()['amount'])
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/credit-card/load-funds.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
