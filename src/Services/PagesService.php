<?php

namespace App\Services;

use App\Entity\PagesList;
use App\Form\PagesAdminFormType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PagesService extends AbstractController{
    
    function PageManager(ManagerRegistry $doctrine, Request $request, bool $newPage, String $page_id = null){

        // Création / Récupération d'une page
        // --------------------------------------------------------
        if ($newPage) {
            $page = new PagesList();
        } else {
            $entityManager = $doctrine->getManager();
            $page = $entityManager->getRepository(PagesList::class)->findOneBy(['page_id' => $page_id]);
            if(!$page) {
                throw $this->createNotFoundException(
                    "Aucune page n'a été trouvée"
                );
            }
        }


        // Initialisation du formulaire
        // --------------------------------------------------------
        $form = $this->createForm(PagesAdminFormType::class, $page);
        $form->handleRequest($request);


        // Envoi du formulaire
        // --------------------------------------------------------
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $page = $form->getData();

            // Création du slug
            $slugify = new Slugify();
            $slugName = $slugify->slugify($form->get('page_name')->getData());
            $slugUrl = $slugify->slugify($form->get('page_url')->getData());

            // Création de l'ID Page
            if ($newPage) {
                $page->setPageId($slugName);
            }

            // Création de l'URL
            if (!$form->get('page_meta_title')->getData() || $newPage) {
                $page->setPageUrl($slugName);
            } elseif (!$form->get('page_meta_title')->getData() || !$newPage) {
                $page->setPageUrl($page->getPageUrl());
            } else {
                $page->setPageMetaTitle($slugUrl);
            }

            // Création du Meta Title
            if (!$form->get('page_meta_title')->getData()) {
                $page->setPageMetaTitle($form->get('page_name')->getData());
            } else {
                $page->setPageMetaTitle($form->get('page_meta_title')->getData());
            }

            // Création / Modification du fichier TWIG
            if ($newPage) {
                $file = fopen("../templates/webpages/pages/fr/" . $slugName . '.html.twig', 'w');
                $file_en = fopen("../templates/webpages/pages/en/" . $slugName . '.html.twig', 'w');
            } else {
                $pageFileName = $page->getPageId() . '.html.twig';
                unlink("../templates/webpages/pages/fr/" . $pageFileName);
                unlink("../templates/webpages/pages/en/" . $pageFileName);
                $file = fopen("../templates/webpages/pages/fr/" . $pageFileName, 'w');
                $file_en = fopen("../templates/webpages/pages/en/" . $pageFileName, 'w');
            }
            fwrite($file, $form->get('page_content')->getData());
            fclose($file);
            fwrite($file_en, $form->get('page_content_en')->getData());
            fclose($file_en);

            // Envoi des données vers la BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($page);
            $entityManager->flush();
        }

        return $form;
    }

}