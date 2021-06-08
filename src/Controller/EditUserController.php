<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditUserController extends AbstractController
{
    /**
     * @Route("/edit/user", name="edit_user")
     */
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
           
            
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            return $this->redirectToRoute('edit_user');

        }

            return $this->render('edit_user/index.html.twig', [
                'editForm' => $form->createView()
            ]);
        
    }


    // }
}
