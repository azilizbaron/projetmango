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
use App\Repository\InscriptionRepository;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
    public function reporterCourse(Circuit $courseEnfant,MailerInterface $mailer, UserRepository $repo): Response{
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

        //Ajout de touts les participants dans le tableau
        $tabParticipants = $repo-> inscritCourse($courseEnfant);
        // array_push($tabParticipants, $repo->inscritCourse($courseAdulte));

        foreach($tabParticipants as $participant){
        //construction et envoie du mail pour prévenir du changement de date
            $email=(new Email())
                ->from("projetmangopoec@gmail.com")
                ->to($participant->getEmail())
                ->subject("Numéro de licence")
                ->text(
                "Bonjour,  
                merci de bien vouloir indiquer votre numéro le licence afin de pouvoir particier à la prochaine course de motocross.
                Si celle-ci n'est pas indiqué la veille de l'évènement nous serons contraint d'anuler votre inscrition. 
                En vous remerciant pour votre compréhension. 
                Cordialement, l'équipe MX PARC");
            $mailer->send($email);
        }

        return $this->render('admin/course/test.html.twig', [
            'courseEnfant' => $courseEnfant,
        ]);
       // return $this->redirectToRoute('admin_consulter_course');
    }

    /**
     * @Route("/admin/course/participants/{course}", name="admin_consulter_participants")
     */
    public function consulterParticipants(UserRepository $repo, Circuit $course ) : Response{
        
        $tabParticipants = $repo-> inscritCourse($course);
        return $this->render('admin/course/participants.html.twig',[
            "participants" => $tabParticipants,
            "course" => $course 
        ]);
    }

    /**
     * @Route("/admin/course/supp/{user}/{circuit}", name="admin_supprimer_participants" , methods ="delete")
     */
    public function supprimerParticipants(InscriptionRepository $repoI, User $user, Circuit $circuit, Request $request, UserRepository $repoU){

        //Pour la sécurité on vérifie si avec la requête de supression, on a bien le token
        if ($this->isCsrfTokenValid("SUP".$user->getId().$circuit->getId(), $request->request->get('_token'))) {
            $repoI->deleteInscription($user, $circuit);
        }
    
        //récupération des données pour envoyer la vue
        $tabParticipants = $repoU-> inscritCourse($circuit);
        return $this->render('admin/course/participants.html.twig',[
            "participants" => $tabParticipants,
            "course" => $circuit 
        ]);
    }

    /**
     * @Route("/admin/couse/participants/pdf/{course}", name="admin_pdf")
     */
    public function genererPdf(UserRepository $repo, Circuit $course ){
        // les participants à la course
        $tabParticipants = $repo-> inscritCourse($course);

        //création du pdf
        $dompdf= new Dompdf();

        //récupération de la vue    
        $html = $this->renderView("admin/course/pdf.html.twig", [
            "participants" => $tabParticipants,
            "date" => $course->getDate()
        ]);
        
        //passer du html au pdf
        $dompdf->loadHtml($html);

        //options
        $dompdf->setPaper("A4", "portrait");
        $dompdf->render();
        //Afficher le pdf dans le navigateur
        $dompdf->stream("listeEmargement.pdf",[
            "Attachment"=>false
        ]);
    }

    /**
     * @Route("/admin/courses/participants/{circuit}/{user}", name="admin_mail_licence")
     */
    public function envoyerMailLicence(User $user, MailerInterface $mailer, UserRepository $repo, Circuit $circuit){
        //construction et envoie du mail
        $email=(new Email())
            ->from("projetmangopoec@gmail.com")
            ->to($user->getEmail())
            ->subject("Numéro de licence")
            ->text(
            "Bonjour,  
            merci de bien vouloir indiquer votre numéro le licence afin de pouvoir particier à la prochaine course de motocross.
            Si celle-ci n'est pas indiqué la veille de l'évènement nous serons contraint d'anuler votre inscrition. 
            En vous remerciant pour votre compréhension. 
            Cordialement, l'équipe MX PARC");
        $mailer->send($email);
        
        //récupération des données pour envoyer la vue
        $tabParticipants = $repo-> inscritCourse($circuit);
        return $this->render('admin/course/participants.html.twig',[
            "participants" => $tabParticipants,
            "course" => $circuit 
        ]);
    }
    
   
}
