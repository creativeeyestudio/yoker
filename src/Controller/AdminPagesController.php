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
    public function index(): Response
    {
        return $this->render('pages/index.html.twig', [
            'controller_name' => 'PagesController',
        ]);
    }

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
            $entityManager->persist($page);
            $entityManager->flush();

            // Création du fichier
            $file = fopen("../templates/webpages/pages/" . $pageFileName, 'w');
            fwrite($file, $pageContent);
            fclose($file);

            // Redirection vers la page crée
            return $this->redirectToRoute('admin_pages');
        }

        return $this->render('pages/add-page.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PagesController',
        ]);
    }

    #[Route('/admin/pages/modifier', name: 'admin_pages_modify')]
    public function modify_page() {
        $form = $this->createForm(PagesAdminFormType::class);
        
        return $this->render('pages/modify-page.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PagesController',
        ]);
    }
}
