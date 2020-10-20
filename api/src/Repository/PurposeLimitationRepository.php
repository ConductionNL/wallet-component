<?php

namespace App\Repository;

use App\Entity\PurposeLimitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PurposeLimitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurposeLimitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurposeLimitation[]    findAll()
 * @method PurposeLimitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurposeLimitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurposeLimitation::class);
    }

    // /**
    //  * @return PurposeLimitation[] Returns an array of PurposeLimitation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PurposeLimitation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
