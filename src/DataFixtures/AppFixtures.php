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
        $circuit5->setDate(new DateTime("07/04/2021"))
        ->setNbPlaces(75);
        $manager->persist($circuit5);

        $circuit6 = new Circuit();
        $circuit6->setDate(new DateTime("07/04/2021"))
        ->setNbPlaces(15);
        $manager->persist($circuit6);

        //tableau avec tous les circuits adultes
        $circuitsAdultes=[$circuit1,$circuit3,$circuit5];

        //tableau avec tous les circuits enfants
        $circuitsEnfants=[$circuit2,$circuit4, $circuit6];

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
                        ->setCircuitId($ca)
                        ->setListeAttente(false);
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
                        ->setCircuitId($ce)
                        ->setListeAttente(false);
            $manager->persist($inscription);
          }
        }

        $manager->flush();
    }
}
