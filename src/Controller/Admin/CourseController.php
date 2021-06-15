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
use App\Form\CircuitType;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Utils\Utils;

class CourseController extends AbstractController
{
    /**
     * @Route("/admin/course", name="admin_consulter_course")
     */
    public function consulterCourse(Utils $utils): Response
    {
        $courses = $utils->dateProchaineCourse();
        return $this->render('admin/course/index.html.twig', [
            'courses' => $courses,
        ]);
    }
    
    /**
     * @Route("/admin/course/ajouter", name="admin_ajouter_course")
     */
    public function ajouterCourse(EntityManagerInterface $em, Request $request, CircuitRepository $repo, Utils $utils){

        $form = $this->createForm(CircuitType::class);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $test = true;
            //On vérifie que la course n'ai pas déjà été crée

            //on récupère tous les potentiels circuits avec la même date
            $testDate = $repo->findBy(["date" => $form->get("date")->getData()]);
            //si il y a bien un circuit à la même date
            if($testDate==[]){ //si le tableau est vide ->pas de course 
                $circuit = new Circuit;
                $circuit->setDate($form->get("date")->getData());
                $circuit->setNbPlaces($form->get("nb_places")->getData());
                $em->persist($circuit);
                $em->flush();
                $this->addFlash('success','Nouvelle course créée'); 
            }
            else{
                //on va vérifier qu'il n'y en as pas avec le même nombre de places 
                foreach($testDate as $key => $value){
                    //si il y  bien un circuit avec la même date 
                    if($testDate[$key]->getNbPlaces() == $form->get("nb_places")->getData()){
                    //on envoie un message disant que la course a déjà été crée 
                        $this->addFlash('success', 'La course existe déjà');
                        $test = false;
                    }
                }
                if($test){
                        $circuit = new Circuit;
                        $circuit->setDate($form->get("date")->getData());
                        $circuit->setNbPlaces($form->get("nb_places")->getData());
                        $em->persist($circuit);
                        $em->flush();
                        $this->addFlash('success','Nouvelle course créée'); 
                }
            }
            $courses = $utils->dateProchaineCourse();
            return $this->render('admin/course/index.html.twig', [
                'courses' => $courses,
            ]);
        }

        return $this->render('admin/course/ajoutCourse.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/course/{course}", name="admin_reporter_course")
     */
    public function reporterCourse(Circuit $course,MailerInterface $mailer, UserRepository $repo, EntityManagerInterface $em){
       $date = $course->getDate();
       $date2= date_modify($date,'+1 week');
       $date2 = new \DateTime( $date->format("Y-m-d"));
       $course-> setDate($date2);
       $em->persist($course);
       $em->flush();
       $this->addFlash('success','La course a bien été reportée');

        //Ajout de touts les participants dans le tableau
       $tabParticipants = $repo->inscritCourse($course);

      foreach($tabParticipants as $participant){
        //construction et envoie du mail pour prévenir du changement de date
            $email=(new Email())
                ->from("projetmangopoec@gmail.com")
               // ->to($participant->getEmail())
                ->to("projetmangopoec@gmail.com")
                ->subject("La course a été reportée")
                ->text(
                "Bonjour,
                La course à laquelle vous vous êtes inscrit viens d'être reportée d'une semaine.
                En vous remerciant de votre compréhension.
                Cordialement, l'équipe MX PARC");
            $mailer->send($email);
        }

        return $this->render('admin/course/index.html.twig', [
            'courses' => $course,
        ]);
    }

    /**
     * @Route("/admin/course/participants/{course}", name="admin_consulter_participants")
     */
    public function consulterParticipants(UserRepository $repo, Circuit $course ) : Response{
        
        $tabParticipants = $repo-> inscritCourse($course);
        return $this->render('admin/course/participants.html.twig',[
            "participants" => $tabParticipants,
            "course" => $course, 
            "places"=> $course->getNbPlaces()
        ]);
    }

    /**
     * @Route("/admin/course/supp/{user}/{circuit}", name="admin_supprimer_participants" , methods ="delete")
     */
    public function supprimerParticipants(InscriptionRepository $repoI, UserRepository $repoU, User $user, Circuit $circuit, 
    Request $request, EntityManagerInterface $em, MailerInterface $mailer){

        //Pour la sécurité on vérifie si avec la requête de supression, on a bien le token
        if ($this->isCsrfTokenValid("SUP".$user->getId().$circuit->getId(), $request->request->get('_token'))) {
            $repoI->deleteInscription($user, $circuit);
            //envoyer un mail 
            $emailSupp=(new Email())
                ->from("projetmangopoec@gmail.com")
                //->to($user->getEmail())
                ->to("projetmangopoec@gmail.com")
                ->subject("Désinscription de la course")
                ->text(
                "Bonjour,
                Nous sommes au regret de vous annoncer que vous n'êtes plus inscrit à la prochaine course.
                Pour plus d'informations, merci de vous référer aux administrateurs.
                Cordialement, l'équipe MX PARC");
                $mailer->send($emailSupp);
        }
    
        //gestion de la liste d'attente 
        //récupération de la liste des inscrits
        $tabParticipants= $repoU->inscritCourseListeAttente($circuit);
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
               $emailInscrit=(new Email())
                ->from("projetmangopoec@gmail.com")
               // ->to($participantU->getEmail())
               ->to("projetmangopoec@gmail.com")
                ->subject("Inscritpion a la course : vous n'êtes plus sûr liste d'attente")
                ->text(
                "Bonjour,
                nous avons le plaisir de vous annoncer que vous êtes officiellement inscrit à la prochaine course de motocross, une place venant de se libérer.
                Cordialement, l'équipe MX PARC");
                $mailer->send($emailInscrit);
                
                break;
            }  
        }
      
        //récupération des données pour envoyer la vue
        $tabParticipants = $repoU-> inscritCourse($circuit);
        return $this->render('admin/course/participants.html.twig',[
            "participants" => $tabParticipants,
            "course" => $circuit,
            "places"=> $circuit->getNbPlaces()
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

        if(count($tabParticipants)< $course->getNbPlaces()){
            $places = count($tabParticipants);
        }
        else{
            $places = $course->getNbPlaces();
        }
        
        //récupération de la vue    
        $html = $this->renderView("admin/course/pdf.html.twig", [
            "participants" => $tabParticipants,
            "date" => $course->getDate(),
            "places" => $places
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
           // ->to($user->getEmail())
           ->to("projetmangopoec@gmail.com")
            ->subject("Numéro de licence")
            ->text(
            "Bonjour, 
            merci de bien vouloir indiquer votre numéro la licence afin de pouvoir participer à la prochaine course de motocross.
            Si celle-ci n'est pas indiquée la veille de l'évènement nous serons contraints d'annuler votre inscription.
            En vous remerciant pour votre compréhension.
            Cordialement, l'équipe MX PARC");
        $mailer->send($email);
        
        //récupération des données pour envoyer la vue
        $tabParticipants = $repo-> inscritCourse($circuit);
        return $this->render('admin/course/participants.html.twig',[
            "participants" => $tabParticipants,
            "course" => $circuit, 
            "places"=> $circuit->getNbPlaces()
        ]);
    }  
}