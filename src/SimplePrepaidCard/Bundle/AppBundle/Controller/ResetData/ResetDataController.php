<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\ResetData;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;

class ResetDataController extends Controller
{
    /**
     * @Config\Route("/reset-data", name="reset-data")
     * @Config\Security("has_role('ROLE_USER')")
     * @Config\Method({"GET"})
     */
    public function resetDataAction()
    {
        $process = new Process('../bin/console simple-credit-card:setup-data');
        $process->run();

        return $this->redirectToRoute('index');
    }
}
