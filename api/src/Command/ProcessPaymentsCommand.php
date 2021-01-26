<?php

// src/Command/CreateUserCommand.php

namespace App\Command;

use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessPaymentsCommand extends Command
{
    private $em;
    private $paymentService;

    public function __construct(EntityManagerInterface $em, PaymentService $paymentService)
    {
        $this->em = $em;
        $this->paymentService = $paymentService;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:process:payments')
            // the short description shown while running "php bin/console list"
            ->setDescription('Command to process all the payments for the organizations');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->paymentService->processPayments();

        return Command::SUCCESS;
    }
}
