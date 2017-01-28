<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Controller\Security;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Config\Route("/", name="index")
     * @Config\Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        $roles = $this->getUser()->getRoles();

        if (in_array('ROLE_HOLDER', $roles) || in_array('ROLE_CUSTOMER', $roles)) {
            return $this->redirect($this->generateUrl('customer'));
        }

        return $this->redirect($this->generateUrl('merchant'));
    }

    /**
     * @Config\Route("/login", name="login")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('@App/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Config\Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        return $this->redirect($this->generateUrl('login'));
    }

    /**
     * @Config\Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        return $this->redirect($this->generateUrl('login'));
    }
}
