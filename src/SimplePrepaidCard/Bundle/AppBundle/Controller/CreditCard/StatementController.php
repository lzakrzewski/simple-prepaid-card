<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\CreditCard;

use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatementController extends Controller
{
    /**
     * @Config\Route("/statement", name="statement")
     * @Config\Method({"GET"})
     */
    public function unblockFundsAction()
    {
        return $this->render('@App/credit-card/statement.html.twig', [
            'statement' => $this->get('simple_prepaid_card.credit_card.query.statement')
                ->get(Uuid::fromString('6a45032e-738a-48b7-893d-ebdc60d0c3b7')),
        ]);
    }
}
