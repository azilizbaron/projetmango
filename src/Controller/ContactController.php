<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            
            $message = (new Email())
                ->from($contactFormData['Email'])
                ->to('projetmangopoec@gmail.com')
                ->subject('Formulaire de contact site')
                ->text('Sender : '.$contactFormData['Email'].\PHP_EOL.
                    $contactFormData['Message'],
                    'text/plain');
            $mailer->send($message);
            $this->addFlash('success', 'Vore message a bien été envoyé');
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]); 
    }
}
