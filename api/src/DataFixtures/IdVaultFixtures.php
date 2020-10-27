<?php

namespace App\DataFixtures;

use App\Entity\Claim;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class IdVaultFixtures extends Fixture
{
    private $params;
    /**
     * @var CommonGroundService
     */
    private $commonGroundService;

    public function __construct(ParameterBagInterface $params, CommonGroundService $commonGroundService)
    {
        $this->params = $params;
        $this->commonGroundService = $commonGroundService;
    }

    public function load(ObjectManager $manager)
    {
        if (
            !$this->params->get('app_build_all_fixtures') &&
            $this->params->get('app_domain') != 'id-vault.com' && strpos($this->params->get('app_domain'), 'id-vault.com') == false &&
            $this->params->get('app_domain') != 'zuiddrecht.nl' && strpos($this->params->get('app_domain'), 'zuiddrecht.nl') == false &&
            $this->params->get('app_domain') != 'zuid-drecht.nl' && strpos($this->params->get('app_domain'), 'zuid-drecht.nl') == false
        ) {
            return false;
        }

        // ...
        $id = Uuid::fromString('5dc88f62-bacc-40b5-bf11-9db9b2a8da5c');
        $claim = new Claim();
        $claim->setPerson(); //841949b7-7488-429f-9171-3a4338b541a6
        $manager->persist($claim);
        $claim->setId($id);
        $manager->persist($claim);
        $manager->flush();
        $claim = $manager->getRepository('App:Claim')->findOneBy(['id'=> $id]);
    }
}
