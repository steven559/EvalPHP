<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @Route("/register", name="register")
     */
    public function inscrit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createFormBuilder($user)->add('username', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('password', PasswordType::class)
            ->add('role', ChoiceType::class, [
                'label' => 'Attribution de rôle: ',
                'choices' => [
                    'User' => 'ROLE_USER']])
            ->getForm();
        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createFormBuilder($user)->add('username', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
                ->add('password', PasswordType::class)
                ->add('role', ChoiceType::class, [
                    'label' => 'Attribution de rôle: ',
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN']
                ])
                ->getForm();
        }

        //->createForm(UserType::class, $user);
        $form->handleRequest($request);
        //on encode le password si il est ok
        if ($form->isSubmitted() && $form->isValid()) {
           if ($this->isGranted('ROLE_ADMIN')) {
                $user->setRole('ROLE_ADMIN');


           }
           else {
               $user->setRole('ROLE_USER');
           }
            $user->setPassword(


                $form->get('password')->getData(),

                );


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('register');
        }
        return $this->render('user/Inscrit.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
