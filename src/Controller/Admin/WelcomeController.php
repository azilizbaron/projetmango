<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;




class WelcomeController extends AbstractController
{
    /**
     * @Route("/admin/welcome", name="admin_welcome")
     */
    public function index(): Response
    {
        return $this->render('admin/welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
        ]);
    }
}
