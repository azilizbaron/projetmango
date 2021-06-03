<?php

namespace App\DataFixtures;

use App\Entity\Circuit;
use App\Entity\Inscription;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {     
      //création du faker
        $faker = \Faker\Factory::create("fr_FR");

        // création des circuits
        $circuit1 = new Circuit();
        $circuit1->setDate(new DateTime("05/02/2021"))
        ->setNbPlaces(75);
        $manager->persist($circuit1);

        $circuit2 = new Circuit();
        $circuit2->setDate(new DateTime("04/04/2021"))
        ->setNbPlaces(15);
        $manager->persist($circuit2);

        $circuit3 = new Circuit();
        $circuit3->setDate(new DateTime("06/06/2021"))
        ->setNbPlaces(75);
        $manager->persist($circuit3);

        $circuit4 = new Circuit();
        $circuit4->setDate(new DateTime("06/06/2021"))
        ->setNbPlaces(15);
        $manager->persist($circuit4);

        $circuit5 = new Circuit();
        $circuit5->setDate(new DateTime("04/07/2021"))
        ->setNbPlaces(75);
        $manager->persist($circuit5);

        $circuit6 = new Circuit();
        $circuit6->setDate(new DateTime("04/07/2021"))
        ->setNbPlaces(15);
        $manager->persist($circuit6);

        //tableau avec tous les circuits adultes
        $circuitsAdultes=[$circuit1,$circuit3,$circuit5];

        //tableau avec tous les circuits enfants
        $circuitsEnfants=[$circuit2,$circuit4, $circuit6];


        //Création de l'administrateur
        $userAdmin = new User();
        $userAdmin->setEmail("admin@admin.com")
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword('admin')
        ->setNom('ROBICHET')
        ->setPrenom('Joël')
        ->setTel("06000000")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setNumLicence("6666666666")
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(1);
        $manager->persist($userAdmin);


        //génération de données aléatoires pour crée des utilisateur
        foreach($circuitsAdultes as $ca){
          for($i =0; $i<75; $i++){
            //pour chaque course, création de 75 membres
            $membre = new User();
            $membre->setEmail($faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPassword('motDePasse')
            ->setNom($faker->lastName())
            ->setPrenom($faker->firstName())
            ->setTel($faker->regexify("\d{10}"))
            ->setAdresse($faker->streetAddress())
            ->setCp($faker->regexify("\d{5}"))
            ->setVille($faker->country())
            ->setNumLicence($faker->regexify("\d{0,5}"))
            ->setDateNaissance(new DateTime($faker->date()))
            ->setMembre($faker->regexify("[0-1]"));
            $manager->persist($membre);

            //inscrition du membre à la course 
            $inscription = new Inscription();
            $inscription->setDateInscription($faker->dateTimeThisYear())
                        ->setUserId($membre)
                        ->setCircuitId($ca);
            $manager->persist($inscription);
          }
        }

        foreach($circuitsEnfants as $ce){
          for($i=0; $i<15; $i++){
            //pour chaque courses, création de 15 membres
            $membre = new User();
            $membre->setEmail($faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPassword('motDePasse')
            ->setNom($faker->lastName())
            ->setPrenom($faker->firstName())
            ->setTel($faker->regexify("\d{10}"))
            ->setAdresse($faker->streetAddress())
            ->setCp($faker->regexify("\d{5}"))
            ->setVille($faker->country())
            ->setNumLicence($faker->regexify("\d{0,5}"))
            ->setDateNaissance(new DateTime($faker->date()))
            ->setMembre($faker->regexify("[0-1]"));
            $manager->persist($membre);

            //inscrition du membre à la course 
            $inscription = new Inscription();
            $inscription->setDateInscription($faker->dateTimeThisYear())
                        ->setUserId($membre)
                        ->setCircuitId($ce);
            $manager->persist($inscription);
          }
        }

        
      /*  $membre1 = new User();
        $membre1->setEmail("RIHARD.Lionel@gmail.com")
        ->setRoles(['ROLE_USER'])
        ->setPassword('Lionel')
        ->setNom('RIHARD')
        ->setPrenom('Lionel')
        ->setTel("06000000")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setNumLicence("77777777")
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(1);
        $manager->persist($membre1);

        $membre2 = new User();
        $membre2->setEmail("DRANDA.Daniel@outlook.fr")
        ->setRoles(['ROLE_USER'])
        ->setPassword('Daniel')
        ->setNom('DRANDA')
        ->setPrenom('Daniel')
        ->setTel("06655550")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setNumLicence("77e5587")
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(1);
        $manager->persist($membre2);

        $membre3 = new User();
        $membre3->setEmail("MEDIOUB.Gilbert@hotmail.fr")
        ->setRoles(['ROLE_USER'])
        ->setPassword('Gilbert')
        ->setNom('MEDIOUB')
        ->setPrenom('Gilbert')
        ->setTel("06666688")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setNumLicence("77e55587")
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(1);
        $manager->persist($membre3);

        $nonMembre1 = new User();
        $nonMembre1->setEmail("FUSTINO.Jean-Phillipe@gmail.com")
        ->setRoles(['ROLE_USER'])
        ->setPassword('Jean-Phillipe')
        ->setNom('FUSTINO')
        ->setPrenom('Jean-Phillipe')
        ->setTel("099668")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setNumLicence("77e55587")
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(0);
        $manager->persist($nonMembre1);

        $nonMembre2 = new User();
        $nonMembre2->setEmail("FUTAULLY.Tristan@outlook.fr")
        ->setRoles(['ROLE_USER'])
        ->setPassword('Tristan')
        ->setNom('FUTAULLY')
        ->setPrenom('Tristan')
        ->setTel("09969968")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setNumLicence("77e55587")
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(0);
        $manager->persist($nonMembre2);

        $nonMembre3 = new User();
        $nonMembre3->setEmail("FUTRZYNSKI.Ewen@outlook.fr")
        ->setRoles(['ROLE_USER'])
        ->setPassword('Ewen')
        ->setNom('FUTRZYNSKI')
        ->setPrenom('Ewen')
        ->setTel("099668")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setNumLicence("77e55587")
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(0);
        $manager->persist($nonMembre3);

        $nonMembre4 = new User();
        $nonMembre4->setEmail("test.test@outlook.fr")
        ->setRoles(['ROLE_USER'])
        ->setPassword('Test')
        ->setNom('TEST')
        ->setPrenom('TEST')
        ->setTel("099668")
        ->setAdresse("1 rue du pré")
        ->setCp('35000')
        ->setVille('Rennes')
        ->setDateNaissance(new DateTime("10/12/1979"))
        ->setMembre(0);
        $manager->persist($nonMembre4);

        $inscription1 = new Inscription();
        $inscription1->setDateInscription(new DateTime("10/12/1979"))
        ->setUserId($membre1)
        ->setCircuitId($circuit1);
        $manager->persist($inscription1);

        $inscription2 = new Inscription();
        $inscription2->setDateInscription(new DateTime("2021-12-05"))
        ->setUserId($membre2)
        ->setCircuitId($circuit2);
        $manager->persist($inscription2);

        $inscription3 = new Inscription();
        $inscription3
        ->setDateInscription(new DateTime("2021-05-12"))
        ->setUserId($membre3)
        ->setCircuitId($circuit3);
        $manager->persist($inscription3);

        $inscription4 = new Inscription();
        $inscription4->setDateInscription(new DateTime("2021-05-12"))
        ->setUserId($nonMembre1)
        ->setCircuitId($circuit1);
        $manager->persist($inscription4);

        $inscription5 = new Inscription();
        $inscription5->setDateInscription(new DateTime("2021-05-05"))
        ->setUserId($nonMembre2)
        ->setCircuitId($circuit2);
        $manager->persist($inscription5);

        $inscription6 = new Inscription();
        $inscription6->setDateInscription(new DateTime("2021-06-06"))
        ->setUserId($nonMembre3)
        ->setCircuitId($circuit3);
        $manager->persist($inscription6);

        $inscription6 = new Inscription();
        $inscription6->setDateInscription(new DateTime("2021-06-06"))
        ->setUserId($nonMembre4)
        ->setCircuitId($circuit3)
        ->setCircuitId($circuit1)
        ->setCircuitId($circuit2);
        $manager->persist($inscription6);
        // $product = new Product();
        // $manager->persist($product);

        $inscription1 = new Inscription();
        $inscription1->setDateInscription(new DateTime("1979-10-12 00:00:00"))
                     ->setUserId($membre1)
                     ->setCircuitId($circuit1);
        $manager->persist($inscription1); */

        $manager->flush();
    }
}
