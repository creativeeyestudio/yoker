<?php

namespace App\Controller;

use App\Entity\EmailsList;
use App\Form\EmailAdminFormType;
use App\Repository\EmailsListRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminEmailsController extends AbstractController
{
    private $em;
    private $request;
    private $emailsRepo;

    function __construct(EntityManagerInterface $em, RequestStack $request, EmailsListRepository $emailsRepo)
    {
        $this->em = $em;
        $this->request = $request;
        $this->emailsRepo = $emailsRepo;
    }

    #[Route('/admin/emails', name: 'app_admin_emails')]
    public function index(): Response
    {
        return $this->render('admin_emails/index.html.twig', [
            'emails' => $this->emailsRepo->findAll()
        ]);
    }

    #[Route('/admin/emails/ajouter', name: 'app_admin_emails_create')]
    public function create(): Response
    {
        $form = $this->createForm(EmailAdminFormType::class);
        $form->handleRequest($this->request);

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
            $this->em->persist($email);
            $this->em->flush();
        }

        return $this->render('admin_emails/email_manager.html.twig', [
            'form' => $form->createView(),
            'name' => '',
            'content' => '',
        ]);
    }

    #[Route('/admin/email/{emailId}', name: 'app_admin_emails_update')]
    public function update(string $emailId): Response
    {
        $form = $this->createForm(EmailAdminFormType::class);
        $form->handleRequest($this->request);

        // Récupération de l'E-mail
        $email = $this->emailsRepo->findOneBy(['email_id' => $emailId]);

        if (!$email) {
            throw $this->createNotFoundException('Email not found.');
        }

        $form = $this->createForm(EmailAdminFormType::class, $email);
        $form->handleRequest($this->request);
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
            $this->em->flush();
            $this->em->flush();
        }

        return $this->render('admin_emails/email_manager.html.twig', [
            'form' => $form->createView(),
            'name' => $email->getEmailName(),
            'content' => $emailContent,
        ]);
    }

    #[Route('/admin/email/{emailId}/delete', name: 'app_admin_emails_delete')]
    public function delete(string $emailId)
    {
        // Récupération de l'E-mail
        $email = $this->emailsRepo->findOneBy(['email_id' => $emailId]);

        if (!$email) {
            throw $this->createNotFoundException("L'E-Mail n'a pas été trouvé");
        }

        // Suppression du fichier
        $filePath = "../templates/emails/" . $email->getEmailId() . ".html.twig";
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Suppression dans la DB
        $this->em->remove($email);
        $this->em->flush();

        // Retour à la liste
        return $this->redirectToRoute('app_admin_emails');
    }
}
