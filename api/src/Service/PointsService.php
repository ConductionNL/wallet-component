<?php

namespace App\Service;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

class PointsService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function getPointsByApplication($id)
    {
        try {
            $application = $this->em->getRepository('App:Application')->findOneBy(['id' => $id]);
            return $this->em->getRepository('App:Authorization')->getPointsByApplication($application);
        } catch (\Exception $e) {
            throw new \Exception('Application id is invalid');
        }
    }

    public function getPointsByOrganization($id)
    {
        return $this->em->getRepository('App:Authorization')->getPointsByOrganization($id);
    }

}
