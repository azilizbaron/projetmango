<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CircuitRepository;
use App\Repository\UserRepository;
use App\Entity\Circuit;
use App\Entity\User;
use DateTime;

class CourseController extends AbstractController
{
    /**
     * @Route("/admin/course", name="admin_consulter_course")
     */
    public function consulterCourse(CircuitRepository $repo): Response
    {
        //date du jour
        $date = new DateTime();
        // récupère les courses dont le mois et l'année correspondent à la date du jour
        $courses = $repo->coursesAVenir($date->format('Y-m'));
        $test =  $courses[0]["date"];
        //si les courses du mois courant sont déjà passé
        if(substr($test,-2) < $date->format("d")){
            // si on est en décembre
            if($date->format('m')=="12"){
                $nouvelleAnnee = strval($date->format("Y")+1);
                // la nouvelle date sera en janvier de l'année suivante
                $nouvelleDate = $nouvelleAnnee."-01";
            }else{ // si nous somme un autre mois
                $nouveauMois = $date->format("m")+1;
                //si le nouveau mois est plus petit que 10
                if($nouveauMois < 10){
                    $nouveauMois = "0".$nouveauMois;
                }
                // la nouvelle date sera le mois suivant de l'année en cours
                $nouvelleDate = $date->format("Y") ."-". $nouveauMois; 
            }
           
            $courses = $repo->coursesAVenir($nouvelleDate);
        } 
        return $this->render('admin/course/index.html.twig', [
            'courses' => $courses,
        ]);
    }
    
    /**
     * @Route("/admin/course/{courseEnfant}", name="admin_reporter_course")
     */
    public function reporterCourse(Circuit $courseEnfant): Response{
        $em=$this->getDoctrine()->getManager();
        //je récupère la date de deux courses
        $Date = $courseEnfant->getDate();
        //j'avance la date de 7 jours
        $nouvelleDate = date_modify($Date,'+7 day');
        //Je met a jour la date
        $courseEnfant->setDate($nouvelleDate);

        $em->persist($courseEnfant);

       /* $courseAdulte->setDate(date_modify($courseAdulte->getDate(),'+7 day'));
        $em->persist($courseAdulte);*/
        $em->flush(); 

        return $this->render('admin/course/test.html.twig', [
            'courseEnfant' => $courseEnfant,
        ]);
       // return $this->redirectToRoute('admin_consulter_course');
    }
    /**
     * @Route("/admin/course/participants/{course}", name="admin_consulter_participants")
     */
    public function consulterParticipants(EntityManagerInterface $em, UserRepository $repo, Circuit $course ) : Response{


        //requete sql : 
        $tabParticipants = $repo-> inscritCourse($course);
        dd($tabParticipants);
        return $this->render('admin/course/participants.html.twig',[
            "participants" => $tabParticipants,
        ]);
    }
   
}
