<?php

namespace App\Command;

use App\Services\BackupService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'backup-database',
    description: 'Add a short description for your command',
)]
class BackupDatabaseCommand extends Command
{
    private $backupService;

    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    protected function configure(): void
    {
        $this->setName('app:backup-database')
             ->setDescription('Backup the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->backupService->createBackup();
        $output->writeln('Backup completed successfully.');

        return Command::SUCCESS;
    }
}
