<?php

namespace App\DataFixtures;

use App\Entity\Circuit;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {   $circuit1 = new Circuit();
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
        ->addCircuitId($circuit1)
        ->setMembre(1);
        $manager->persist($userAdmin);

        $membre1 = new User();
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
        ->addCircuitId($circuit1)
        ->addCircuitId($circuit2)
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
        ->addCircuitId($circuit3)
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
        ->addCircuitId($circuit3)
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
        ->addCircuitId($circuit1)
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
        ->addCircuitId($circuit3)
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
        ->addCircuitId($circuit3)
        ->setMembre(0);
        $manager->persist($nonMembre3);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
