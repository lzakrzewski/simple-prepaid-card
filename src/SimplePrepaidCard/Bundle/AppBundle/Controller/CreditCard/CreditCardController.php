<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use SimplePrepaidCard\Bundle\AppBundle\Form\AmountType;
use SimplePrepaidCard\Bundle\AppBundle\Form\CreditCardType;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CreditCard\Application\Command\CreateCreditCard;
use SimplePrepaidCard\CreditCard\Application\Command\LoadFunds;
use SimplePrepaidCard\CreditCard\Model\Holder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

//Todo: Flashbag magic, remove customer coupling, remove redundant actions
class CreditCardController extends Controller
{
    /**
     * @Config\Route("/create-credit-card", name="create-credit-card")
     * @Config\Security("has_role('ROLE_HOLDER')")
     * @Config\Method({"GET", "POST"})
     */
    public function createCreditCardAction(Request $request)
    {
        $form = $this->createForm(CreditCardType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('command_bus')->handle(
                new CreateCreditCard(
                    Uuid::uuid4(),
                    Uuid::fromString(Holder::HOLDER_ID),
                    $form->getData()['card_holder']
                )
            );

            return $this->redirectToRoute('customer');
        }

        return $this->render('@App/credit-card/create-credit-card.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/load-funds", name="load-funds")
     * @Config\Security("has_role('ROLE_HOLDER')")
     * @Config\Method({"GET", "POST"})
     */
    public function loadFundsAction(Request $request)
    {
        $form = $this->createForm(AmountType::class);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->get('command_bus')->handle(
                    new LoadFunds($this->creditCardId(), (int) $form->getData()['amount']->getAmount())
                );

                return $this->redirectToRoute('customer');
            }
        } catch (\Exception $exception) {
            $form->addError(new FormError($exception->getMessage()));
        }

        return $this->render('@App/credit-card/load-funds.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/statement", name="statement")
     * @Config\Security("has_role('ROLE_HOLDER')")
     * @Config\Method({"GET"})
     */
    public function statementAction()
    {
        return $this->render('@App/credit-card/statement.html.twig', [
            'statement' => $this->get('simple_prepaid_card.credit_card.query.statement')
                ->ofHolder(Uuid::fromString(Holder::HOLDER_ID)),
        ]);
    }

    private function creditCardId(): UuidInterface
    {
        return $this->get('simple_prepaid_card.credit_card.query.credit_card_id_of_holder')
            ->get(Uuid::fromString(Holder::HOLDER_ID));
    }
}
