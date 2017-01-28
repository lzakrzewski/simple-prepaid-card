<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\CoffeeShop;

use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use SimplePrepaidCard\Bundle\AppBundle\Form\ProductType;
use SimplePrepaidCard\CoffeeShop\Application\Command\BuyProduct;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller
{
    /**
     * @Config\Route("/customer", name="customer")
     * @Config\Security("has_role('ROLE_CUSTOMER')")
     * @Config\Method({"GET"})
     */
    public function customerAction()
    {
        return $this->render('@App/coffee-shop/customer.html.twig');
    }

    /**
     * @Config\Route("/buy-product", name="buy-product")
     * @Config\Security("has_role('ROLE_CUSTOMER')")
     * @Config\Method({"GET", "POST"})
     */
    public function buyProductAction(Request $request)
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get('command_bus')->handle(
                    new BuyProduct(Uuid::fromString(Customer::CUSTOMER_ID), $form->getData()['product_id'])
                );

                return $this->redirectToRoute('customer');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('@App/coffee-shop/buy-product.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
