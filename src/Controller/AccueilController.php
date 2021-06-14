<?php

namespace App\Controller;

use App\Entity\Circuit;
use App\Entity\Inscription;
use App\Entity\User;
use App\Repository\CircuitRepository;
use App\Repository\InscriptionRepository;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(CircuitRepository $repo): Response
    {
        //date du jour
        $date = new DateTime();
        // récupère les courses dont le mois et l'année correspondent à la date du jour
        $courses = $repo->coursesAVenir($date->format('Y-m'));
        $test =  $courses[0]["date"];
        //si les courses du mois courant sont déjà passé
        if (substr($test, -2) < $date->format("d")) {
            // si on est en décembre
            if ($date->format('m') == "12") {
                $nouvelleAnnee = strval($date->format("Y") + 1);
                // la nouvelle date sera en janvier de l'année suivante
                $nouvelleDate = $nouvelleAnnee . "-01";
            } else { // si nous somme un autre mois
                $nouveauMois = $date->format("m") + 1;
                //si le nouveau mois est plus petit que 10
                if ($nouveauMois < 10) {
                    $nouveauMois = "0" . $nouveauMois;
                }
                // la nouvelle date sera le mois suivant de l'année en cours
                $nouvelleDate = $date->format("Y") . "-" . $nouveauMois;
            }

            $courses = $repo->coursesAVenir($nouvelleDate);
            $dateCourse = new DateTime($courses[0]['date']);

        }
        return $this->render('accueil/index.html.twig', [
            'courses' => $courses,
            'dateCourse' => $dateCourse->format('d-m-Y'),
        ]);
    }

    /**
     * @Route("/accueil/inscription", name="inscriptionCourse")
     */
    public function inscriptionCourse(Request $request, MailerInterface $mailer, InscriptionRepository $repo)
    {
        //si utilisateur connecté enregistrement de l'inscription dans la BDD
        if($this->getUser()){

            $entityManager = $this->getDoctrine()->getManager();
            $inscription = new Inscription();

            // récupération de l'objet circuit concerné
            $id = $request->request->get("course");
            $circuit = $entityManager->find(Circuit::class, $id);

            // on vérifie si l'utilisateur est déjà inscrit à la course
            $repository = $this->getDoctrine()->getRepository(Inscription::class);
            $insc = $repository->findOneBy([
                'user' => $this->getUser()->getId(),
                'circuit' => $id,
            ]);
            //si l'utilisateur est déjà inscrit
            if($insc){
                //affiche du message 'déjà inscrit'
                $this->addFlash('success', 'Inscription déjà réalisée');
            }else{
                
                //ajout des informations dans l'objet inscription
                $inscription->setUserId($this->getUser());
                $inscription->setCircuitId($circuit);
                $date = new DateTime();
                $inscription->setDateInscription($date->setTimezone(new DateTimeZone('Europe/Paris')));

                $nbplace = $circuit->getNbPlaces();
                $nbInscrit=count($repo->findBy(["circuit"=>$circuit->getId()]));
                //si il y a plus de place disponible que le nombre d'inscrit
                if($nbplace>$nbInscrit){
                    $inscription->setListeAttente(0);
                    $textMail="Bonjour,
                    Vous êtes bien inscrit à la course du ".$circuit->getdate()->format('d-m-Y')."
                    Cordialement, l'équipe MX PARC";
                }else{
                    $inscription->setListeAttente(1);
                    $textMail= "Bonjour,
                    La course du ".$circuit->getdate()->format('d-m-Y')." a été victime de son succès.
                    Cependant pas de panique, vous êtes bien sur la liste d'attente. 
                    Si une place se libère vous en serez avertis par mail !
                    Cordialement, l'équipe MX PARC";
                }
                //envoie dans la BDD
                $entityManager->persist($inscription);
                $entityManager->flush();

                //message de confirmation
                $this->addFlash('success', 'Inscription réussie');

                //envoie du mail de confirmation
                $email=(new Email())
                    ->from("projetmangopoec@gmail.com")
                 //   ->to($this->getUser()->getEmail())
                    ->to("projetmangopoec@gmail.com")
                    ->subject("Confirmation : inscription à la prochaine course")
                    ->text($textMail);
                $mailer->send($email);
            }

        }else{ // sinon redirection à la page connection
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('accueil');
    }
}
