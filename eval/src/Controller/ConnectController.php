<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class ConnectController extends AbstractController
{
    /**
     * @Route("connect/login", name="login")
     */
    public function login(Request $request , AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        dump($authenticationUtils);
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('connect/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);

    }
}
