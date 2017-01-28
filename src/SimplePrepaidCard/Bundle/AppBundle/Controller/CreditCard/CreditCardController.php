<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use SimplePrepaidCard\Bundle\AppBundle\Form\CreditCardType;
use SimplePrepaidCard\Bundle\AppBundle\Form\FundsType;
use SimplePrepaidCard\Bundle\AppBundle\Form\SaveType;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CreditCard\Application\Command\BlockFunds;
use SimplePrepaidCard\CreditCard\Application\Command\ChargeFunds;
use SimplePrepaidCard\CreditCard\Application\Command\CreateCreditCard;
use SimplePrepaidCard\CreditCard\Application\Command\LoadFunds;
use SimplePrepaidCard\CreditCard\Application\Command\UnblockFunds;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

//Todo: Flashbag magic, remove customer coupling, remove redundant actions
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
                new CreateCreditCard(Uuid::uuid4(), Uuid::fromString(Customer::CUSTOMER_ID), $form->getData()['card_holder'])
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
        $form = $this->createForm(FundsType::class);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->get('command_bus')->handle(
                    new LoadFunds($this->creditCardId(), (int) $form->getData()['amount'])
                );

                return $this->redirectToRoute('homepage');
            }
        } catch (\Exception $exception) {
            $form->get('amount')->addError(new FormError($exception->getMessage()));
        }

        return $this->render('@App/credit-card/load-funds.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/block-funds", name="block-funds")
     * @Config\Method({"GET", "POST"})
     */
    public function blockFundsAction(Request $request)
    {
        $form = $this->createForm(FundsType::class);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->get('command_bus')->handle(
                    new BlockFunds($this->creditCardId(), (int) $form->getData()['amount'])
                );

                return $this->redirectToRoute('homepage');
            }
        } catch (\Exception $exception) {
            $form->get('amount')->addError(new FormError($exception->getMessage()));
        }

        return $this->render('@App/credit-card/block-funds.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/unblock-funds", name="unblock-funds")
     * @Config\Method({"GET", "POST"})
     */
    public function unblockFundsAction(Request $request)
    {
        $form = $this->createForm(SaveType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('command_bus')->handle(
                new UnblockFunds($this->creditCardId())
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/credit-card/unblock-funds.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/charge-funds", name="charge-funds")
     * @Config\Method({"GET", "POST"})
     */
    public function chargeFundsAction(Request $request)
    {
        $form = $this->createForm(FundsType::class);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->get('command_bus')->handle(
                    new ChargeFunds($this->creditCardId(), (int) $form->getData()['amount'])
                );

                return $this->redirectToRoute('homepage');
            }
        } catch (\Exception $exception) {
            $form->get('amount')->addError(new FormError($exception->getMessage()));
        }

        return $this->render('@App/credit-card/charge-funds.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function creditCardId(): UuidInterface
    {
        return $this->get('simple_prepaid_card.credit_card.query.credit_card_id_of_holder')->get(Uuid::fromString(Customer::CUSTOMER_ID));
    }
}
