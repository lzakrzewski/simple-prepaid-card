<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\CoffeeShop;

use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use SimplePrepaidCard\Bundle\AppBundle\Form\FundsType;
use SimplePrepaidCard\CoffeeShop\Application\Command\CaptureAuthorization;
use SimplePrepaidCard\CoffeeShop\Application\Command\RefundCaptured;
use SimplePrepaidCard\CoffeeShop\Application\Command\ReverseAuthorization;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class MerchantController extends Controller
{
    /**
     * @Config\Route("/merchant", name="merchant")
     * @Config\Method({"GET"})
     */
    public function merchantAction()
    {
        return $this->render('@App/coffee-shop/merchant.html.twig');
    }

    /**
     * @Config\Route("/capture-authorization", name="capture-authorization")
     * @Config\Method({"GET", "POST"})
     */
    public function captureAuthorizationAction(Request $request)
    {
        $form = $this->createForm(FundsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get('command_bus')->handle(
                    new CaptureAuthorization(Uuid::fromString(Merchant::MERCHANT_ID), (int) $form->getData()['amount'])
                );

                return $this->redirectToRoute('homepage');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('@App/coffee-shop/capture-authorization.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/reverse-authorization", name="reverse-authorization")
     * @Config\Method({"GET", "POST"})
     */
    public function reverseAuthorizationAction(Request $request)
    {
        $form = $this->createForm(FundsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get('command_bus')->handle(
                    new ReverseAuthorization(Uuid::fromString(Merchant::MERCHANT_ID), (int) $form->getData()['amount'])
                );

                return $this->redirectToRoute('homepage');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('@App/coffee-shop/reverse-authorization.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Config\Route("/refund-captured", name="refund-captured")
     * @Config\Method({"GET", "POST"})
     */
    public function refundCapturedAction(Request $request)
    {
        $form = $this->createForm(FundsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get('command_bus')->handle(
                    new RefundCaptured(Uuid::fromString(Merchant::MERCHANT_ID), (int) $form->getData()['amount'])
                );

                return $this->redirectToRoute('homepage');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('@App/coffee-shop/refund-captured.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
