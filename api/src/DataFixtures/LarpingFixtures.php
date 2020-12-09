<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Authorization;
use App\Entity\Claim;
use App\Entity\Proof;
use App\Entity\PurposeLimitation;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LarpingFixtures extends Fixture
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
            $this->params->get('app_domain') != 'zuid-drecht.nl' && strpos($this->params->get('app_domain'), 'zuid-drecht.nl') == false &&
            $this->params->get('app_domain') != 'larping.eu' && strpos($this->params->get('app_domain'), 'larping.eu') == false
        ) {
            return false;
        }
        // Commonground
        $id = Uuid::fromString('c243189f-11cf-40c5-8859-6a57555328bf');
        $application = new Application();
        $application->setName('Larping');
        $application->setSecret('eeb22abf509d45c59ddf97c8c39b67ae');
        $application->setDescription('Larping application');
        $application->setAuthorizationUrl('https://dev.larping.eu/auth/idvault');
        $application->setSingleSignOnUrl('https://dev.larping.eu/auth/idvault');
        $application->setOrganization($this->commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => '7b863976-0fc3-4f49-a4f7-0bf7d2f2f535'])); // Larping
        $application->setContact($this->commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'applications', 'id' => '9798cae6-187a-434f-bd66-f1dc2cc61466'])); // Larping
        $manager->persist($application);
        $application->setId($id);
        $manager->persist($application);
        $manager->flush();
        $application = $manager->getRepository('App:Application')->findOneBy(['id'=> $id]);
    }
}
