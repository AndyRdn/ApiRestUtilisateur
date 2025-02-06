<?php

// src/Command/SyncUsersCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\FirebaseUserSyncService;

#[AsCommand(
    name: 'app:sync-users',
    description: 'Description be'
)]
class SyncUsersCommand extends Command
{
    protected static $defaultName = 'app:sync-users';

    private FirebaseUserSyncService $syncService;

    public function __construct(FirebaseUserSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting initial sync...');
        $this->syncService->syncUsers();
        $output->writeln('Initial sync completed!');

        // Keep listening for changes
        $output->writeln('Listening for real-time changes...');
        $this->syncService->syncUsers(); // This will now listen continuously

        return Command::SUCCESS;
    }
}