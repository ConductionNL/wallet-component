<?php

namespace App\Service;

use Conduction\BalanceBundle\Service\BalanceService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    private $em;
    private $commonGroundService;
    private $balanceService;

    public function __construct(EntityManagerInterface $em, CommonGroundService $commonGroundService, BalanceService $balanceService)
    {
        $this->em = $em;
        $this->commonGroundService = $commonGroundService;
        $this->balanceService = $balanceService;
    }

}
