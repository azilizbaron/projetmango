<?php

namespace App\Controller;

use App\Form\EditUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditUserController extends AbstractController
{
    /**
     * @Route("/edit/user", name="edit_user")
     */
    public function index(): Response
    {
        $form = $this->createForm(EditUserType::class);


        return $this->render('edit_user/index.html.twig', [
            'editForm' => $form->createView()
        ]);
    }
}
