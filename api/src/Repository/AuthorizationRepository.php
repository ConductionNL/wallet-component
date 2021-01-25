<?php

namespace App\Repository;

use App\Entity\Authorization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Authorization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Authorization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Authorization[]    findAll()
 * @method Authorization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Authorization::class);
    }

    public function getPointsByApplication($application)
    {
        $points = $this->createQueryBuilder('p')
        ->select('SUM(p.points) as points')
        ->andWhere('p.application = :application')
        ->setParameter('application', $application)
        ->getQuery()
        ->getResult();

        return $points;
    }

    public function getPointsByOrganization($organization)
    {
        $points = $this->createQueryBuilder('p')
        ->select('SUM(p.points) as points')
        ->leftJoin('p.application', 'application')
        ->andWhere('application.organization = :organization')
        ->setParameter('organization', $organization)
        ->getQuery()
        ->getResult();

        return $points;
    }
}
