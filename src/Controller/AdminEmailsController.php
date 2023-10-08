<?php

namespace App\Controller;

use App\Entity\EmailsList;
use App\Form\EmailAdminFormType;
use App\Repository\EmailsListRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminEmailsController extends AbstractController
{
    #[Route('/admin/emails', name: 'app_admin_emails')]
    public function index(EmailsListRepository $emailsRepo): Response
    {
        $emails = $emailsRepo->findAll();

        return $this->render('admin_emails/index.html.twig', [
            'emails' => $emails
        ]);
    }

    #[Route('/admin/emails/ajouter', name: 'app_admin_emails_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EmailAdminFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des valeurs
            $emailName = $form->get('email_name')->getData();
            $emailContent = $form->get('email_content')->getData();

            // Création de l'ID
            $slugify = new Slugify();
            $emailId = $slugify->slugify($emailName);

            // Création du template
            $filePath = "../templates/emails/" . $emailId . '.html.twig';
            file_put_contents($filePath, $emailContent);

            // Envoi des données vers la BDD
            $email = new EmailsList();
            $email->setEmailName($emailName);
            $email->setEmailId($emailId);
            $em->persist($email);
            $em->flush();
        }

        return $this->render('admin_emails/email_manager.html.twig', [
            'form' => $form->createView(),
            'name' => '',
            'content' => '',
        ]);
    }

    #[Route('/admin/email/{emailId}', name: 'app_admin_emails_update')]
    public function update(Request $request, EntityManagerInterface $em, string $emailId)
    {
        $form = $this->createForm(EmailAdminFormType::class);
        $form->handleRequest($request);

        // Récupération de l'E-mail
        $emailRepository = $em->getRepository(EmailsList::class);
        $email = $emailRepository->findOneBy(['email_id' => $emailId]);

        if (!$email) {
            throw $this->createNotFoundException('Email not found.');
        }

        $form = $this->createForm(EmailAdminFormType::class, $email);
        $form->handleRequest($request);
        $emailContent = file_get_contents("../templates/emails/" . $emailId . ".html.twig");

        if ($form->isSubmitted() && $form->isValid()) {
            $emailName = $form->get('email_name')->getData();
            $emailContent = $form->get('email_content')->getData();
            
            // Modification du fichier
            $filePath = "../templates/emails/" . $email->getEmailId() . '.html.twig';
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            file_put_contents($filePath, $emailContent);

            // Envoi des données vers la BDD
            $email->setEmailName($emailName);
            $em->flush();
        }

        return $this->render('admin_emails/email_manager.html.twig', [
            'form' => $form->createView(),
            'name' => $email->getEmailName(),
            'content' => $emailContent,
        ]);
    }

    #[Route('/admin/email/{emailId}/delete', name: 'app_admin_emails_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, string $emailId)
    {
        // Récupération de l'E-mail
        $emailRepository = $entityManager->getRepository(EmailsList::class);
        $email = $emailRepository->findOneBy(['email_id' => $emailId]);

        if (!$email) {
            throw $this->createNotFoundException("L'E-Mail n'a pas été trouvé");
        }
        
        // Suppression du fichier
        $filePath = "../templates/emails/" . $email->getEmailId() . ".html.twig";
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Suppression dans la DB
        $entityManager->remove($email);
        $entityManager->flush();

        // Retour à la liste
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
