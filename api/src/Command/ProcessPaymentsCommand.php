<?php

// src/Command/CreateUserCommand.php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessPaymentsCommand extends Command
{
    private $em;
    private $paymentService;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('process:payment')
            // the short description shown while running "php bin/console list"
            ->setDescription('Command to process all the payments for the organizations');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {


        return Command::SUCCESS;
    }
}
