<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route("/accueil/inscription", name="inscriptionCourse")
     */
    public function inscriptionCourse(){

        $inscription = new Inscription();
        
        $inscription->setUserId($this->getUser());
        $inscription->setCircuitId(21);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($inscription);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }
        
    
}
