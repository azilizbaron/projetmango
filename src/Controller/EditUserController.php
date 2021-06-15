<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\CircuitRepository;
use App\Repository\InscriptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;


class EditUserController extends AbstractController
{
    /**
     * @Route("/edit/user", name="edit_user")
     */
    public function index(Request $request, CircuitRepository $repoC, InscriptionRepository $repoI, 
    UserRepository $repoU, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        //gestion de la modification de l'utilisateur 

        //formulaire de modification
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

        //gestion de la suppression de l'inscription
        //l'id de l'inscription + la date de la dernière inscription
        $inscription = $repoC->derniereCourseInscrit($user->getId());
        //Si n'a rien récupéré
        if($inscription == []){
            $inscriptionDate = null;
            $inscriptionId=null;
        }
        else{ 
            $inscriptionDate = $inscription["0"]["date"];
            $inscriptionId=$inscription["0"]["idInscription"]; 
        }
        //supression
        if($this->isCsrfTokenValid("SUP".$inscriptionId,$request->request->get('_token'))){
            $em->remove($repoI->find($inscriptionId));
            $em->flush();
            $this->addFlash('success','Désinscription confirmée');

            //mail de confirmation de désinscription
            $emailDesinscrit=(new Email())
                ->from("projetmangopoec@gmail.com")
                ->to("projetmangopoec@gmail.com")
                //->to($user->getEmail())
                ->subject("Confirmation de désinscription")
                ->text(
                    "Bonjour,
                    nous avons le plaisir de vous annoncer que vous n'êtes officiellement plus inscrit à la prochaine course de motocross.
                    Cordialement, l'équipe MX PARC");
            $mailer->send($emailDesinscrit);

            //ajout dela liste d'attente

            //récupération de la liste des inscrits
            $tabParticipants= $repoU->inscritCourseListeAttente($repoC->find($inscription["0"]["idCourse"]));
            //récupération de la personne avec un 1 dans liste d'attente
            foreach($tabParticipants as $participant){
                if($participant["listeAttente"]==1){
                    
                    //On récupère les informations liée à la table inscription
                    $participantI= $repoI->findOneBy(["user" =>$participant["id"]]);
                    $inscription=$repoI->find($participantI);
                        //on remplace le 1 par un 0
                    $inscription->setListeAttente(0);
                    
                    //On sauvegarde
                    $em->persist($inscription);
                    $em->flush();             

                    //Consutruction du mail 
                    //On récupère les informations liées à la table user
                    $participantU=$repoU->find($participant["id"]);
                    //envoie du mail 
                    $emailAttente=(new Email())
                        ->from("projetmangopoec@gmail.com")
                        ->to("projetmangopoec@gmail.com")
                        //->to($participantU->getEmail())
                        ->subject("Inscritpion à la course : vous n'êtes plus sur liste d'attente")
                        ->text(
                        "Bonjour,
                        nous avons le plaisir de vous annoncer que vous êtes officiellement inscrit à la prochaine course de motocross, une place venant de se libérer.
                        Cordialement, l'équipe MX PARC");
                    $mailer->send($emailAttente);
                        
                    break;
                }  
            }
            return $this->redirectToRoute('edit_user');
        }

        return $this->render('edit_user/index.html.twig', [
            'editForm' => $form->createView(),
            'inscriptionDate' =>$inscriptionDate ,
            'inscriptionId' => $inscriptionId,
        ]);
        
    }

}
