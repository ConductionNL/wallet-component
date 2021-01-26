<?php

namespace App\Service;

use App\Entity\Authorization;
use Conduction\BalanceBundle\Service\BalanceService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;

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

    /**
     * This function processes the payments for the organizations
     */
    public function processPayments() {
        $organizations = $this->getOrganizations();

        foreach ($organizations as $organization) {
            $url = $this->commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $organization['id']]);
            $points = (int)$this->em->getRepository('App:Authorization')->getPointsByOrganization($organization['@id'])[0]['points'];
            $this->balanceService->removeCredit(Money::EUR($points), $url, 'Id-vault payment');
            $this->createInvoice((string)$points / 100, $url);
        }

        $this->resetPoints();
    }

    /**
     * This function gets the organizations with an account linked to them
     * @return array Array of organizations with an account linked to them
     */
    public function getOrganizations() {
        $organizations = $this->commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])['hydra:member'];

        $results = array_filter($organizations, function ($organization) {
            $url = $this->commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $organization['id']]);
            $account = $this->balanceService->getAcount($url);
            return $account ? true : false;
        });

        return $results;
    }

    /**
     * This function creates an invoice for the payment
     *
     * @param string $price price of the invoice
     * @param string $customer uri of the organization
     */
    public function createInvoice(string $price, string $customer) {
        $invoice = [];
        $invoice['name'] = 'id-vault monthly payment';
        $invoice['price'] = $price;
        $invoice['priceCurrency'] = 'EUR';
        $invoice['customer'] = $customer;
        $this->commonGroundService->createResource($invoice, ['component' => 'bc', 'type' => 'invoices']);
    }

    /**
     * This functions resets the points of all authorizations to the current count of scopes
     */
    public function resetPoints() {
        $authorizations = $this->em->getRepository('App:Authorization')->findAll();
        foreach ($authorizations as $authorization) {
            if ($authorization instanceof Authorization) {
                $authorization->setPoints(count($authorization->getScopes()));
                $this->em->persist($authorization);
                $this->em->flush();
            }
        }
    }

}
