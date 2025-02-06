<?php

// src/Command/FirestoreDebugCommand.php
namespace App\Command;

use Kreait\Firebase\Contract\Firestore;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:debug-firestore')]
class ValidateFirebaseCommand extends Command
{
    public function __construct(private Firestore $firestore)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $database = $this->firestore->database();
            $output->writeln('Successfully connected to Firestore!');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('Error: '.$e->getMessage());
            return Command::FAILURE;
        }
    }
}