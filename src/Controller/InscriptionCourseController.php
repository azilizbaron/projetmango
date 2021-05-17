<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionCourseController extends AbstractController
{
    
     /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render("inscription_course/index.html.twig");
    }
    
    
    /**
     * @Route("/inscription/course", name="inscription_course")
     */
    public function inscription(): Response
    {
        if(empty($_SESSION['id']))
        {
        return $this->render("security/login.html.twig");
        }

    }
    


        // else ($user_connecte = true)
        // {
        //     return $this->render('inscription_course/index.html.twig', [
        //         'connecte' => $user_connecte,  
        // }

    
        // ]);
}
