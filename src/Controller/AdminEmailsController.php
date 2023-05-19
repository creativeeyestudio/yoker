<?php

namespace App\Controller;

use App\Entity\EmailsList;
use App\Form\EmailAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminEmailsController extends AbstractController
{
    #[Route('/admin/emails', name: 'app_admin_emails')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $emailsRepo = $em->getRepository(EmailsList::class);
        $emails = $emailsRepo->findAll();

        return $this->render('admin_emails/index.html.twig', [
            'emails' => $emails
        ]);
    }

    #[Route('/admin/emails/ajouter', name: 'app_admin_emails_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(EmailAdminFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des valeurs
            $email = new EmailsList();
            $emailName = $form->get('email_name')->getData();
            $emailContent = $form->get('email_content')->getData();

            // Création de l'ID
            $slugify = new Slugify();
            $emailId = $slugify->slugify($emailName);

            // Création du template
            $file = fopen("../templates/emails/" . $emailId . '.html.twig', 'w');
            fwrite($file, $emailContent);
            fclose($file);

            // Envoi des données vers la BDD
            $email->setEmailName($emailName);
            $email->setEmailId($emailId);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($email);
            $entityManager->flush();
        }

        return $this->render('admin_emails/email_manager.html.twig', [
            'form' => $form->createView(),
            'name' => '',
            'content' => '',
        ]);
    }

    #[Route('/admin/email/{emailId}', name: 'app_admin_emails_update')]
    public function update(Request $request, ManagerRegistry $doctrine, string $emailId)
    {
        $form = $this->createForm(EmailAdminFormType::class);
        $form->handleRequest($request);

        // Récupération de l'E-mail
        $entityManager = $doctrine->getManager();
        $email = $entityManager->getRepository(EmailsList::class)->findOneBy(['email_id' => $emailId]);
        $emailContent = file_get_contents("../templates/emails/" . $emailId . ".html.twig");

        if ($form->isSubmitted() && $form->isValid()) {
            $emailName = $form->get('email_name')->getData();
            $emailContent = $form->get('email_content')->getData();
            
            // Modification du fichier
            unlink("../templates/emails/" . $email->getEmailId() . '.html.twig');
            $file = fopen("../templates/emails/" . $email->getEmailId() . '.html.twig', 'w');
            fwrite($file, $emailContent);
            fclose($file);

            // Envoi des données vers la BDD
            $email->setEmailName($emailName);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($email);
            $entityManager->flush();
        }

        return $this->render('admin_emails/email_manager.html.twig', [
            'form' => $form->createView(),
            'name' => $email->getEmailName(),
            'content' => $emailContent,
        ]);
    }

    #[Route('/admin/email/{emailId}/delete', name: 'app_admin_emails_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, string $emailId)
    {
        // Récupération de l'E-mail
        $entityManager = $doctrine->getManager();
        $email = $entityManager->getRepository(EmailsList::class)->findOneBy(['email_id' => $emailId]);
        
        // Suppression du fichier
        unlink("../templates/emails/" . $email->getEmailId() . ".html.twig");

        // Suppresison dans la DB
        $entityManager->remove($email);
        $entityManager->flush();

        // Retour à la liste
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
