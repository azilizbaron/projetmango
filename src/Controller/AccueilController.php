<?php

namespace App\Controller;

use App\Entity\Circuit;
use App\Entity\Inscription;
use App\Entity\User;
use App\Repository\CircuitRepository;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
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
        }
        return $this->render('accueil/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    /**
     * @Route("/accueil/inscription", name="inscriptionCourse")
     */
    public function inscriptionCourse(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $inscription = new Inscription();
        $id = $request->request->get("course");
        $circuit = $entityManager->find(Circuit::class, $id);
        $inscription->setUserId($this->getUser());
        $inscription->setCircuitId($circuit);
        $date = new DateTime();
        $inscription->setDateInscription($date->setTimezone(new DateTimeZone('Europe/Paris')));
        $entityManager->persist($inscription);
        $entityManager->flush();
        return $this->redirectToRoute('accueil');
    }
}
