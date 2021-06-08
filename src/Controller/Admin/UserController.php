<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\RechercheUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user")
     */
    public function recherche_user(UserRepository $repo, Request $request): Response
    {
        $users = null;

        //formulaire de recherche 
        $form= $this->createForm(RechercheUserType::class);
        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //rechercher les utilisateurs correspondant au mot clef
           $users = $repo->search($search->get('mot')->getData());
        }

        return $this->render('admin/user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $users
        ]);
    }
    
    /**
     * @Route("/admin/user/{user}", name="admin_voir_user")
     */
    public function voir_user(User $user):Response{
        return $this->render('admin/user/user.html.twig',[
            'user'=> $user
        ]);
    }
}
