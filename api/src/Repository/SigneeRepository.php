<?php

namespace App\Repository;

use App\Entity\Signee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Signee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Signee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Signee[]    findAll()
 * @method Signee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SigneeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Signee::class);
    }

    // /**
    //  * @return Signee[] Returns an array of Signee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Signee
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
