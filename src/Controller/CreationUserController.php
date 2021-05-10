<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreationUserController extends AbstractController
{
    /**
     * @Route("/creation/user", name="creation_user")
     */
    public function index(): Response
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
                ->add('Nom','text')
                ->add('Prénom','text')
                ->add('email','email', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Email(),
                    )
                ))
                ->add('Valider', 'submit')
                ->getForm();
        
        $form->handleRequest();

        if($form->isValid()){
            return new Response('Formulaire valide');
        }

        return array(
            'creation_user'=>$form->createView(),
            'titre'=>'Inscription');



        // return $this->render('creation_user/index.html.twig', [
        //     'controller_name' => 'CreationUserController',
        // ]);
    }
}
