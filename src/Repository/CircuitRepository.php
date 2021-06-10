<?php

namespace App\Repository;

use App\Entity\Circuit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Circuit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Circuit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Circuit[]    findAll()
 * @method Circuit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CircuitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Circuit::class);
    }

    //Retourne la course enfant et la course Adulte a venir
    public function coursesAVenir($date) {
       $conn = $this->getEntityManager()->getConnection();
        $rawSql ="SELECT * FROM circuit where date like :date";
        $params =array (':date' => $date.'-%');
        $stmt = $conn->prepare($rawSql);
        $stmt->execute($params);
        return $stmt->fetchAll(); 
       /* $date = $date."-%";
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            ' SELECT id FROM App\Entity\Circuit where date like :date '
        )->setParameter(':date', $date); 
        return $query->getResult();*/

    }

    //SELECT circuit.date from circuit join inscription on 
    //circuit.id = inscription.circuit_id join user on user.id = inscription.user_id where user.id like 1086
    public function derniereCourseInscrit($idUser){
        return $this->createQueryBuilder('c')
        ->select('i.id as idInscription','c.date','c.id as idCourse')
        ->leftJoin('c.inscriptions', 'i')
        ->where('i.user =:user')
        ->setParameter('user',$idUser)
      //  ->orderBy('ASC')
        ->getQuery()
        ->execute();
    }

    // /**
    //  * @return Circuit[] Returns an array of Circuit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Circuit
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
