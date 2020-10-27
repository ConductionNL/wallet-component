<?php

namespace App\DataFixtures;

use App\Entity\Claim;
use App\Entity\Contract;
use App\Entity\Dossier;
use App\Entity\Proof;
use App\Entity\PurposeLimitation;
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

        // Test Claim 1
        $id = Uuid::fromString('1acebad8-67ee-46fc-ab87-fd17e2ad72bb');
        $claim1 = new Claim();
        $claim1->setPerson($this->commonGroundService->cleanUrl(['component'=>'cc', 'type'=>'people', 'id'=>'841949b7-7488-429f-9171-3a4338b541a6'])); // Jan@zwarteraaf.nl
        $claim1->setProperty('job title');
        $claim1->setData([
            "jobTitle" => "professor"
        ]);
        // Maybe we should generate this token :) but this is just random test data:
        $claim1->setToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJsb2dnZWRJbkFzIjoiYWRtaW4iLCJpYXQiOjE0MjI3Nzk2Mzh9.gzSraSYS8EXBxLN_oWnFSRgCzcmJmMjLiuyu5CSpyHI');
        $manager->persist($claim1);
        $claim1->setId($id);
        $manager->persist($claim1);
        $manager->flush();
        $claim1 = $manager->getRepository('App:Claim')->findOneBy(['id'=> $id]);

        // Test Proof 1.1
        $id = Uuid::fromString('b71fd063-4f70-4993-b34e-011863c515fd');
        $proof = new Proof();
        $proof->setType('gmail');
        $proof->setProofPurpose('assertionMethod');
        $proof->setVerificationMethod('https://example.wac/issuers/keys/1.1');
        $proof->setJws('eyJhbGciOiJSUzI1NiIsImI2NCI6ZmFsc2UsImNyaXQiOlsiYjY0Il19TCYt5XsITJX1CxPCT8yAV-TVkIEq_PbChOMqsLfRoPsnsgw5WEuts01mq-pQy7UJiN5mgRxD-WUcX16dUEMGlv50aqzpqh4Qktb3rk-BuQy72IFLOqV0G_zS245-kronKb78cPN25DGlcTwLtjPAYuNzVBAh4vGHSrQyHUdBBPM');
        $proof->setClaim($claim1);
        $manager->persist($proof);
        $proof->setId($id);
        $manager->persist($proof);
        $manager->flush();
        $manager->getRepository('App:Proof')->findOneBy(['id'=> $id]);

        // Test Proof 1.2
        $id = Uuid::fromString('09a19b6d-a809-4bbc-be7e-78b32c2179ae');
        $proof = new Proof();
        $proof->setType('facebook');
        $proof->setProofPurpose('assertionMethod');
        $proof->setVerificationMethod('https://example.wac/issuers/keys/1.2');
        $proof->setJws('eyJhbGciOiJSUzI1NiIsImI2NCI6ZmFsc2UsImNyaXQiOlsiYjY0Il19TCYt5XsITJX1CxPCT8yAV-TVkIEq_PbChOMqsLfRoPsnsgw5WEuts01mq-pQy7UJiN5mgRxD-WUcX16dUEMGlv50aqzpqh4Qktb3rk-BuQy72IFLOqV0G_zS245-kronKb78cPN25DGlcTwLtjPAYuNzVBAh4vGHSrQyHUdBBPM');
        $proof->setClaim($claim1);
        $manager->persist($proof);
        $proof->setId($id);
        $manager->persist($proof);
        $manager->flush();
        $manager->getRepository('App:Proof')->findOneBy(['id'=> $id]);

        // Test Claim 2
        $id = Uuid::fromString('59731d56-a3a3-4a1e-9b36-296ea03dd190');
        $claim2 = new Claim();
        $claim2->setPerson($this->commonGroundService->cleanUrl(['component'=>'cc', 'type'=>'people', 'id'=>'841949b7-7488-429f-9171-3a4338b541a6'])); // Jan@zwarteraaf.nl
        $claim2->setProperty('email addresses');
        $claim2->setData([
            "email1" => "jan@zwarteraaf.nl",
            "email2" => "janZwarteraaf@gmail.com"
        ]);
        // Maybe we should generate this token :) but this is just random test data:
        $claim2->setToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJsb2dnZWRJbkFzIjoiYWRtaW4iLCJpYXQiOjE0MjI3Nzk2Mzh9.gzSraSYS8EXBxLN_oWnFSRgCzcmJmMjLiuyu5CSpyHI');
        $manager->persist($claim2);
        $claim2->setId($id);
        $manager->persist($claim2);
        $manager->flush();
        $claim2 = $manager->getRepository('App:Claim')->findOneBy(['id'=> $id]);

        // Test Proof 2.1
        $id = Uuid::fromString('2ecc103b-084e-4465-8be2-8890e3cffdfe');
        $proof = new Proof();
        $proof->setType('gmail');
        $proof->setProofPurpose('assertionMethod');
        $proof->setVerificationMethod('https://example.wac/issuers/keys/2.1');
        $proof->setJws('eyJhbGciOiJSUzI1NiIsImI2NCI6ZmFsc2UsImNyaXQiOlsiYjY0Il19TCYt5XsITJX1CxPCT8yAV-TVkIEq_PbChOMqsLfRoPsnsgw5WEuts01mq-pQy7UJiN5mgRxD-WUcX16dUEMGlv50aqzpqh4Qktb3rk-BuQy72IFLOqV0G_zS245-kronKb78cPN25DGlcTwLtjPAYuNzVBAh4vGHSrQyHUdBBPM');
        $proof->setClaim($claim2);
        $manager->persist($proof);
        $proof->setId($id);
        $manager->persist($proof);
        $manager->flush();
        $manager->getRepository('App:Proof')->findOneBy(['id'=> $id]);

        // Test Proof 2.2
        $id = Uuid::fromString('0273f8bb-073c-4591-a4ba-4ed82ff6c9fa');
        $proof = new Proof();
        $proof->setType('facebook');
        $proof->setProofPurpose('assertionMethod');
        $proof->setVerificationMethod('https://example.wac/issuers/keys/2.2');
        $proof->setJws('eyJhbGciOiJSUzI1NiIsImI2NCI6ZmFsc2UsImNyaXQiOlsiYjY0Il19TCYt5XsITJX1CxPCT8yAV-TVkIEq_PbChOMqsLfRoPsnsgw5WEuts01mq-pQy7UJiN5mgRxD-WUcX16dUEMGlv50aqzpqh4Qktb3rk-BuQy72IFLOqV0G_zS245-kronKb78cPN25DGlcTwLtjPAYuNzVBAh4vGHSrQyHUdBBPM');
        $proof->setClaim($claim2);
        $manager->persist($proof);
        $proof->setId($id);
        $manager->persist($proof);
        $manager->flush();
        $manager->getRepository('App:Proof')->findOneBy(['id'=> $id]);

        // Test Contract
        $id = Uuid::fromString('49ff9063-c080-48a0-a398-701cec0814c0');
        $contract = new Contract();
        $contract->setPerson($this->commonGroundService->cleanUrl(['component'=>'cc', 'type'=>'people', 'id'=>'841949b7-7488-429f-9171-3a4338b541a6'])); // Jan@zwarteraaf.nl
        $contract->setScope([
            "job title",
            "email addresses"
        ]);
        $contract->setGoal('Get the job and email for a job offer on a vacancy site');
        $contract->setApplication($this->commonGroundService->cleanUrl(['component'=>'wrc', 'type'=>'applications', 'id'=>'22888b97-d12b-4505-9a20-ee9cc148d442'])); // id-vault
        $manager->persist($contract);
        $contract->setId($id);
        $manager->persist($contract);
        $manager->flush();
        $contract = $manager->getRepository('App:Contract')->findOneBy(['id'=> $id]);

        $contract->addClaim($claim1);
        $contract->addClaim($claim2);

        // Test Purpose Limitation
        $id = Uuid::fromString('9a42c6bc-f6a4-472e-a905-3ffc5721c45a');
        $purposeLimitation = new PurposeLimitation();
        $purposeLimitation->setName('PurposeLimitation');
        $purposeLimitation->setDescription('the purpose limitation for this contract');
        $purposeLimitation->setData([
            "testdata" => "testdata"
        ]);
        $dateInterval = new \DateInterval('P1Y1M1D');
        $purposeLimitation->setNoticePeriod($dateInterval);
        $dateInterval = new \DateInterval('P1Y2M5D');
        $purposeLimitation->setExpiryPeriod($dateInterval);
        $purposeLimitation->setContract($contract);
        $manager->persist($purposeLimitation);
        $purposeLimitation->setId($id);
        $manager->persist($purposeLimitation);
        $manager->flush();
        $manager->getRepository('App:PurposeLimitation')->findOneBy(['id'=> $id]);

        // Test Dossier
        $id = Uuid::fromString('e428ac4b-d90c-4af7-bc5d-2ebc9569a31e');
        $dossier = new Dossier();
        $dossier->setBasis('An ongoing internship contract');
        $date = new \DateTime();
        $date->add(new \DateInterval('P5M'));
        $dossier->setEndDate($date);
        $dossier->setUrl('https://dev.conduction.academy/');
        $dossier->setLegal(true);
        $dossier->setContract($contract);
        $manager->persist($dossier);
        $dossier->setId($id);
        $manager->persist($dossier);
        $manager->flush();
        $manager->getRepository('App:Dossier')->findOneBy(['id'=> $id]);
    }
}
