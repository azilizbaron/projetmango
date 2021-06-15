<?php
namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Circuit;

class Utils{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Permet d'obtenir la prochaine cours à venir en fonction de la date du système
     */
    public function dateProchaineCourse(){
      //  $manager = $this->getDoctrine()->getManager();
        $repo = $this->entityManager->getRepository(Circuit::class);
        //date du jour
        $date = new \DateTime();
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
        return $courses;
    }
}