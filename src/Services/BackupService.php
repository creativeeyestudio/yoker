<?php

namespace App\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupService extends AbstractController
{
    private $databaseUrl;
    private $mailer;
    private $params;
    private $uploadsPath;
    private $uploadedZip;
    private $sqlPath;
    private $zipPath;

    function __construct(string $databaseUrl, MailerInterface $mailer, ParameterBagInterface $params)
    {
        $this->databaseUrl = $databaseUrl;
        $this->mailer = $mailer;
        $this->params = $params;
        $this->uploadsPath = $this->params->get('kernel.project_dir') . "/public/uploads";
        $this->uploadedZip = $this->uploadsPath . '/upl_' . date("m-Y") . '.zip';
        $this->sqlPath = './src/Backups/db_backup_' . date("m-Y") . '.sql';
        $this->zipPath = './src/Backups/upl_' . date("m-Y") . '.zip';
    }

    public function createBackup()
    {
        // Dump de la base de données
        $databaseDumpCommand = $this->getMysqlDumpCommand();
        $process = new Process($databaseDumpCommand);
        $process->run();

        // Vérifier si la commande a échoué
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->getUploadsFolderToZip();
        $this->sendDumpByMail();
    }

    private function getUploadsFolderToZip()
    {
        $zip = new ZipArchive();
        $zip->open($this->uploadedZip, ZipArchive::CREATE);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->uploadsPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($this->uploadsPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        // Vérifier si le fichier ZIP a été créé
        if (file_exists($this->uploadedZip)) {
            // Déplacer le fichier ZIP dans le dossier 'Backups'
            $filesystem = new Filesystem();
            $filesystem->rename(
                $this->uploadedZip,
                $this->getParameter('kernel.project_dir') . '\src\Backups\upl_' . date("m-Y") . '.zip',
                true
            );
        }        
    }

    private function getMysqlDumpCommand(): array
    {
        // Analyser l'URL de la base de données depuis la configuration Symfony (.env).
        $url = parse_url($this->databaseUrl);
    
        // Extraire les composants nécessaires de l'URL.
        $dbHost = $url['host'] ?? '';
        $dbPort = $url['port'] ?? '';
        $dbUser = $url['user'] ?? '';
        $dbPassword = $url['pass'] ?? '';
        $dbName = ltrim($url['path'], '/');

        // Chemin complet pour sauvegarder le fichier SQL.
        $dumpFilePath = $this->sqlPath ?? './backup.sql';

        // Construire et retourner la commande pour mysqldump avec les paramètres nécessaires.
        return [
            $this->getParameter('mysqldump_path'), // Chemin complet vers l'exécutable mysqldump (paramétré dans les paramètres Symfony).
            '-h' . $dbHost, // Option -h pour spécifier l'hôte de la base de données.
            '-P' . $dbPort, // Option -P pour spécifier le port de la base de données.
            '-u' . $dbUser, // Option -u pour spécifier l'utilisateur de la base de données.
            '-p' . $dbPassword, // Option -p pour spécifier le mot de passe de la base de données.
            $dbName, // Nom de la base de données à sauvegarder.
            '--result-file=' . $dumpFilePath, // Option --result-file pour spécifier le chemin où sauvegarder le fichier SQL.
        ];
    }

    private function sendDumpByMail()
    {
        // Création d'une nouvelle instance de l'objet Email.
        $email = (new Email())
            ->from("no-reply@creative-eye.fr") // Adresse e-mail de l'expéditeur.
            ->to('creative.eye.fr@gmail.com') // Adresse e-mail du destinataire.
            ->subject("Backup mensuel du site " . $this->getParameter('site_name')) // Sujet de l'e-mail.
            ->text("Ci-joint le Backup mensuel du site " . $this->getParameter('site_name')) // Corps du message texte de l'e-mail.
            ->attachFromPath($this->zipPath, 'upl_backup_' . date("m-Y") . '.zip') // Joindre le fichier de sauvegarde avec un nom spécifique basé sur la date.
            ->attachFromPath($this->sqlPath, 'db_backup_' . date("m-Y") . '.sql'); // Joindre le fichier de sauvegarde avec un nom spécifique basé sur la date.

        // Utilisation du service Mailer de Symfony pour envoyer l'e-mail.
        $this->mailer->send($email);
    }
}
