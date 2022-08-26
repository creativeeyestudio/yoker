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
        $form = $this->createForm(PagesAdminFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $slugify = new Slugify();
            $data = $form->getData();
            $pageName = $data['page_name'];
            $pageUrl = $data['page_url'];
            $pageId = $slugify->slugify($pageName);
            $pageContent = $data['page_content'];
            $pageMetaTitle = $data['page_meta_title'];
            $pageMetaDesc = $data['page_meta_desc'];
            $pageFileName = $pageId . ".html.twig";

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $page = new PagesList();
            $page->setPageName($pageName);
            if($pageUrl != null){
                $page->setPageUrl($pageUrl);
            } else {
                $page->setPageUrl($pageId);
            }
            $page->setPageId($pageId);
            if ($pageMetaTitle != null) {
                $page->setPageMetaTitle($pageMetaTitle);
            } else {
                $page->setPageMetaTitle($pageName);
            }
            $page->setPageMetaDesc($pageMetaDesc);
            $page->setBlockedPage('0');
            $entityManager->persist($page);
            $entityManager->flush();

            // Création du fichier
            $file = fopen("../templates/webpages/pages/" . $pageFileName, 'w');
            fwrite($file, $pageContent);
            fclose($file);

            // Redirection vers la page crée
            return $this->redirectToRoute('admin_pages_modify', array('page_id' => $pageId));
        }

        return $this->render('pages/add-page.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AdminPagesController',
        ]);
    }

    /* MODIFIER UNE PAGE
    ------------------------------------------------------- */
    #[Route('/admin/pages/modifier/{page_id}', name: 'admin_pages_modify')]
    public function modify_page(ManagerRegistry $doctrine, Request $request, String $page_id) {
        $form = $this->createForm(PagesAdminFormType::class);
        $form->handleRequest($request);

        // Récupération de la page souhaitée
        $entityManager = $doctrine->getManager();
        $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);
        if(!$page) {
            throw $this->createNotFoundException(
                "Aucune page n'a été trouvée"
            );
        }

        // Récupération du contenu de la page
        $pageContent = file_get_contents("../templates/webpages/pages/" . $page_id . ".html.twig");

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $data = $form->getData();
            $pageName = $data['page_name'];
            $pageUrl = $data['page_url'];
            $pageId = $page->getPageId();
            $pageContent = $data['page_content'];
            $pageMetaTitle = $data['page_meta_title'];
            $pageMetaDesc = $data['page_meta_desc'];
            $pageFileName = $pageId . ".html.twig";

            // Modification des données de la page
            $entityManager = $doctrine->getManager();
            $page->setPageName($pageName);
            if($pageUrl != null){
                $page->setPageUrl($pageUrl);
            } else {
                $page->setPageUrl($pageId);
            }
            if ($pageMetaTitle != null) {
                $page->setPageMetaTitle($pageMetaTitle);
            } else {
                $page->setPageMetaTitle($pageName);
            }
            $page->setPageMetaDesc($pageMetaDesc);
            $entityManager->persist($page);
            $entityManager->flush();
            
            // Modification du contenu de la page
            unlink("../templates/webpages/pages/" . $page_id . ".html.twig");
            $file = fopen("../templates/webpages/pages/" . $pageFileName, 'w');
            fwrite($file, $pageContent);
            fclose($file);

            // Redirection vers la page crée
            return $this->redirectToRoute('admin_pages_modify', array('page_id' => $pageId));
        }

        return $this->render('pages/modify-page.html.twig', [
            'form' => $form->createView(),
            'pageName' => $page->getPageName(),
            'pageUrl' => $page->getPageUrl(),
            'pageId' => $page->getPageId(),
            'pageContent' => $pageContent,
            'pageMetaTitle' => $page->getPageMetaTitle(),
            'pageMetaDesc' => $page->getPageMetaDesc(),
            'controller_name' => 'AdminPagesController',
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
