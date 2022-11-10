<?php

namespace App\Controller;

use App\Entity\PagesList;
use App\Form\PagesAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPagesController extends AbstractController
{
    #[Route('/admin/pages', name: 'admin_pages')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $pagesRepo = $entityManager->getRepository(PagesList::class);
        $pages = $pagesRepo->findAll();

        return $this->render('pages/index.html.twig', [
            'controller_name' => 'AdminPagesController',
            "pages" => $pages
        ]);
    }

    /* AJOUTER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/ajouter', name: 'admin_pages_add')]
    public function add_page(ManagerRegistry $doctrine, Request $request) {
        $page = new PagesList();
        $form = $this->createForm(PagesAdminFormType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $page = $form->getData();

            // Création du slug
            $slugify = new Slugify();
            $slugName = $slugify->slugify($form->get('page_name')->getData());
            $slugUrl = $slugify->slugify($form->get('page_url')->getData());

            // Création de l'ID Page
            $page->setPageId($slugName);

            // Création de l'URL
            if (!$form->get('page_meta_title')->getData()) {
                $page->setPageUrl($slugName);
            } else {
                $page->setPageMetaTitle($slugUrl);
            }

            // Création du Meta Title
            if (!$form->get('page_meta_title')->getData()) {
                $page->setPageMetaTitle($form->get('page_name')->getData());
            } else {
                $page->setPageMetaTitle($form->get('page_meta_title')->getData());
            }

            // Page bloquée
            $page->setBlockedPage(0);

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($page);
            $entityManager->flush();

            // Création du fichier
            $file = fopen("../templates/webpages/pages/" . $slugName . '.html.twig', 'w');
            fwrite($file, $form->get('page_content')->getData());
            fclose($file);

            // Redirection vers la page crée
            return $this->redirectToRoute('admin_pages_modify', ['page_id' => $page->getPageId($slugName)]);
        }

        return $this->render('pages/add-page.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /* MODIFIER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/modifier/{page_id}', name: 'admin_pages_modify')]
    public function modify_page(ManagerRegistry $doctrine, Request $request, String $page_id) {

        // Récupération de la page souhaitée
        $entityManager = $doctrine->getManager();
        $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);
        if(!$page) {
            throw $this->createNotFoundException(
                "Aucune page n'a été trouvée"
            );
        }

        $form = $this->createForm(PagesAdminFormType::class, $page);
        $form->handleRequest($request);

        // Récupération du contenu de la page
        $pageContent = file_get_contents("../templates/webpages/pages/" . $page_id . ".html.twig");

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $page = $form->getData();
            $pageId = $page->getPageId();
            $pageFileName = $page->getPageId() . '.html.twig';

            // Création du slug
            $slugify = new Slugify();
            $slugName = $slugify->slugify($form->get('page_name')->getData());
            $slugUrl = $slugify->slugify($form->get('page_url')->getData());

            // Création de l'ID Page
            $page->setPageId($slugName);

            // Création de l'URL
            if (!$form->get('page_url')->getData()) {
                $page->setPageUrl($slugName);
            } else {
                $page->setPageMetaTitle($slugUrl);
            }

            // Création du Meta Title
            if (!$form->get('page_meta_title')->getData()) {
                $page->setPageMetaTitle($form->get('page_name')->getData());
            } else {
                $page->setPageMetaTitle($form->get('page_meta_title')->getData());
            }

            // Page bloquée
            $page->setBlockedPage(0);

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($page);
            $entityManager->flush();
            
            // Modification du contenu de la page
            unlink("../templates/webpages/pages/" . $pageFileName);
            $file = fopen("../templates/webpages/pages/" . $pageFileName, 'w');
            fwrite($file, $form->get('page_content')->getData());
            fclose($file);

            // Redirection vers la page crée
            return $this->redirectToRoute('admin_pages_modify', ['page_id' => $pageId]);
        }

        return $this->render('pages/modify-page.html.twig', [
            'form' => $form->createView(),
            'pageContent' => $pageContent,
        ]);
    }

    /* SUPPRIMER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/supprimer/{page_id}', name: 'admin_pages_delete')]
    public function delete_page(ManagerRegistry $doctrine, String $page_id) {
        // Suppression de la valeur dans la BDD
        $entityManager = $doctrine->getManager();
        $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);

        if(!$page) {
            throw $this->createNotFoundException(
                "Aucune page n'a été trouvée"
            );
        }

        $entityManager->remove($page);
        $entityManager->flush();

        // Suppression du fichier
        unlink("../templates/webpages/pages/" . $page_id . ".html.twig");

        return $this->redirectToRoute('admin_pages');
    }
}
